<?php

declare(strict_types = 1);

namespace App\Github;

use Milo\Github\Api;
use Milo\Github\Http\IClient;
use Milo\Github\Http\Request;
use Milo\Github\Http\Response;
use Milo\Github\OAuth\Token;


final class BetterApi extends Api
{

	/** @var Api[] */
	private array $clients;

	private int $clientIndex = 0;


	/**
	 * @param array<string> $tokens
	 */
	public function __construct(array $tokens, ?IClient $client = null)
	{
		parent::__construct($client);

		if ($tokens === []) {
			throw new \InvalidArgumentException('Parameter github.tokens must contain at least one token.');
		}

		$this->clients = array_map(function (string $token): Api {
			$token = trim($token);

			if ($token === '') {
				throw new \InvalidArgumentException('GitHub token cannot be empty.');
			}

			$api = new Api($this->getClient());
			$api->setToken(new Token($token));

			return $api;
		}, $tokens);
	}


	public function request(Request $request): Response
	{
		$attempts = count($this->clients);
		$response = null;

		for ($attempt = 0; $attempt < $attempts; $attempt++) {
			$response = $this->nextClient()->request($request);

			if (!$this->isRateLimited($response)) {
				return $response;
			}
		}

		if ($response === null) {
			throw new \LogicException('No GitHub API client is available.');
		}

		return $response;
	}


	private function nextClient(): Api
	{
		$client = $this->clients[$this->clientIndex];
		$this->clientIndex = ($this->clientIndex + 1) % count($this->clients);

		return $client;
	}


	private function isRateLimited(Response $response): bool
	{
		return $response->getCode() === Response::S403_FORBIDDEN
			&& $response->getHeader('X-RateLimit-Remaining') === '0';
	}

}
