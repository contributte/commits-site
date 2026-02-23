<?php

declare(strict_types = 1);

namespace Tests;

use App\Github\BetterApi;
use Milo\Github\Http\IClient;
use Milo\Github\Http\Request;
use Milo\Github\Http\Response;
use Tester\Assert;
use Tests\TestCase\PHPStanTestCase;


require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../app/Github/BetterApi.php';


final class BetterApiTest extends PHPStanTestCase
{

	public function testTokensAreRotatedForEveryRequest(): void
	{
		$client = new RecordingClient(
			self::createResponse(Response::S200_OK),
			self::createResponse(Response::S200_OK),
			self::createResponse(Response::S200_OK),
			self::createResponse(Response::S200_OK)
		);

		$api = new BetterApi(['token-1', 'token-2', 'token-3'], $client);
		$request = new Request(Request::GET, 'https://api.github.com/rate_limit');

		$api->request($request);
		$api->request($request);
		$api->request($request);
		$api->request($request);

		$authHeaders = array_map(static fn (Request $request): ?string => $request->getHeader('Authorization'), $client->getRequests());

		Assert::same([
			'token token-1',
			'token token-2',
			'token token-3',
			'token token-1',
		], $authHeaders);
	}


	public function testRateLimitedRequestRetriesWithNextToken(): void
	{
		$client = new RecordingClient(
			self::createResponse(Response::S403_FORBIDDEN, ['X-RateLimit-Remaining' => '0']),
			self::createResponse(Response::S200_OK)
		);

		$api = new BetterApi(['token-a', 'token-b'], $client);
		$response = $api->request(new Request(Request::GET, 'https://api.github.com/rate_limit'));

		Assert::same(Response::S200_OK, $response->getCode());
		Assert::count(2, $client->getRequests());
		Assert::same('token token-a', $client->getRequests()[0]->getHeader('Authorization'));
		Assert::same('token token-b', $client->getRequests()[1]->getHeader('Authorization'));
	}


	public function testAllRateLimitedTokensReturnLastResponse(): void
	{
		$client = new RecordingClient(
			self::createResponse(Response::S403_FORBIDDEN, ['X-RateLimit-Remaining' => '0']),
			self::createResponse(Response::S403_FORBIDDEN, ['X-RateLimit-Remaining' => '0'])
		);

		$api = new BetterApi(['token-a', 'token-b'], $client);
		$response = $api->request(new Request(Request::GET, 'https://api.github.com/rate_limit'));

		Assert::same(Response::S403_FORBIDDEN, $response->getCode());
		Assert::same('0', $response->getHeader('X-RateLimit-Remaining'));
		Assert::count(2, $client->getRequests());
	}


	public function testEmptyTokensAreRejected(): void
	{
		Assert::exception(
			static function (): void {
				new BetterApi([], new RecordingClient);
			},
			\InvalidArgumentException::class
		);
	}


	/**
	 * @param string[] $headers
	 */
	private static function createResponse(int $code, array $headers = []): Response
	{
		$headers += ['Content-Type' => 'application/json'];

		return new Response($code, $headers, '{}');
	}

}


final class RecordingClient implements IClient
{

	/** @var Response[] */
	private array $responses;

	/** @var Request[] */
	private array $requests = [];

	private ?\Closure $onRequest = null;

	private ?\Closure $onResponse = null;


	public function __construct(Response ...$responses)
	{
		$this->responses = $responses;
	}


	public function request(Request $request): Response
	{
		$this->requests[] = $request;

		if ($this->onRequest !== null) {
			($this->onRequest)($request);
		}

		$response = array_shift($this->responses);

		if ($response === null) {
			throw new \RuntimeException('Missing fake response.');
		}

		if ($this->onResponse !== null) {
			($this->onResponse)($response);
		}

		return $response;
	}


	public function onRequest(?callable $callback): static
	{
		$this->onRequest = $callback === null ? null : \Closure::fromCallable($callback);

		return $this;
	}


	public function onResponse(?callable $callback): static
	{
		$this->onResponse = $callback === null ? null : \Closure::fromCallable($callback);

		return $this;
	}


	/** @return Request[] */
	public function getRequests(): array
	{
		return $this->requests;
	}

}


(new BetterApiTest)->run();
