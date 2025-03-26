<?php
namespace ChromeDevtoolsProtocol;

use ChromeDevtoolsProtocol\Exception\ErrorException;
use ChromeDevtoolsProtocol\Exception\RuntimeException;
use ChromeDevtoolsProtocol\Model\Target\CloseTargetRequest;
use ChromeDevtoolsProtocol\Model\Target\DisposeBrowserContextRequest;
use ChromeDevtoolsProtocol\Model\Target\SendMessageToTargetRequest;

/**
 * Session is an isolated context in headless browser (i.e. cookies are not shared), like incognito window.
 *
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 */
class Session implements DevtoolsClientInterface, InternalClientInterface
{

	use DevtoolsClientTrait;

	/** @var DevtoolsClientInterface */
	private $browser;

	/** @var string */
	private $browserContextId;

	/** @var string */
	private $targetId;

	/** @var string */
	private $sessionId;

	/** @var callable[][] */
	private $listeners = [];

	/** @var int */
	private $messageId = 0;

	/** @var object[] */
	private $commandResults = [];

	/** @var object[][] */
	private $eventBuffers = [];

	public function __construct(DevtoolsClientInterface $browser, string $browserContextId, string $targetId, string $sessionId)
	{
		$this->browser = $browser;
		$this->targetId = $targetId;
		$this->sessionId = $sessionId;
		$this->browserContextId = $browserContextId;
	}


	public function close(): void
	{
		$ctx = Context::withTimeout(Context::background(), 10);

		/* bch36: Without the try/catch block, if one of the calls to $this->browser->target() throws an exception, we never get the line that closes $this->browser. Then, when the browser object (DevtoolsClient) is destructed when the script completes, an exception is thrown from the destructor in DevtoolsClient, with no way to catch it. Exception: "Fatal error: Uncaught ChromeDevtoolsProtocol\Exception\LogicException: You must call [ChromeDevtoolsProtocol\DevtoolsClient::close] method to release underlying WebSocket connection." */
		try
		{
			$this->browser->target()->closeTarget(
				$ctx,
				CloseTargetRequest::builder()
					->setTargetId($this->targetId)
					->build()
			);
			$this->browser->target()->disposeBrowserContext(
				$ctx,
				DisposeBrowserContextRequest::builder()
					->setBrowserContextId($this->browserContextId)
					->build()
			);

			$this->browser->close();
		} catch (\Exception $e) {
			$this->browser->close();

			throw $e;
		}
	}

	/**
	 * @internal
	 */
	public function executeCommand(ContextInterface $ctx, string $method, $parameters)
	{
		$messageId = ++$this->messageId;

		$payload = new \stdClass();
		$payload->id = $messageId;
		$payload->method = $method;
		$payload->params = $parameters;

		$this->browser->target()->sendMessageToTarget(
			$ctx,
			SendMessageToTargetRequest::builder()
				->setTargetId($this->targetId)
				->setSessionId($this->sessionId)
				->setMessage(json_encode($payload))
				->build()
		);

		for (; ;) {
			$receivedMessage = $this->browser->target()->awaitReceivedMessageFromTarget($ctx);

			if ($receivedMessage->targetId === $this->targetId && $receivedMessage->sessionId === $this->sessionId) {
				$this->handleMessage(json_decode($receivedMessage->message));
			}

			if (isset($this->commandResults[$messageId])) {
				$result = $this->commandResults[$messageId];
				unset($this->commandResults[$messageId]);
				return $result;
			}
		}
	}

	/**
	 * @internal
	 */
	public function awaitEvent(ContextInterface $ctx, string $method)
	{
		if (!empty($this->eventBuffers[$method])) {
			return array_shift($this->eventBuffers[$method])->params;
		}

		for (; ;) {
			$eventMessage = null;

			$receivedMessage = $this->browser->target()->awaitReceivedMessageFromTarget($ctx);

			if ($receivedMessage->targetId === $this->targetId && $receivedMessage->sessionId === $this->sessionId) {

				$nextEventMessage = $this->handleMessage(json_decode($receivedMessage->message), $method);

				if ($eventMessage === null && $nextEventMessage !== null) {
					$eventMessage = $nextEventMessage;
				}
			}

			if ($eventMessage !== null) {
				return $eventMessage->params;
			}
		}
	}

	/**
	 * @internal
	 */
	private function handleMessage($message, ?string $returnIfEventMethod = null)
	{
		if (isset($message->error)) {
			throw new ErrorException($message->error->message, $message->error->code);

		} else if (isset($message->method)) {
			if (isset($this->listeners[$message->method])) {
				foreach ($this->listeners[$message->method] as $callback) {
					$callback($message->params);
				}
			}

			if ($returnIfEventMethod !== null && $message->method === $returnIfEventMethod) {
				return $message;
			} else {
				if (!isset($this->eventBuffers[$message->method])) {
					$this->eventBuffers[$message->method] = [];
				}
				array_push($this->eventBuffers[$message->method], $message);
			}

		} else if (isset($message->id)) {
			$this->commandResults[$message->id] = $message->result ?? new \stdClass();

		} else {
			throw new RuntimeException(sprintf(
				"Unhandled message: %s",
				json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
			));
		}

		return null;
	}

	public function addListener(string $method, callable $listener): SubscriptionInterface
	{

		if (!isset($this->listeners[$method])) {
			$this->listeners[$method] = [];
		}

		$this->listeners[$method][] = $listener;

		$callback = function () use ($method, $listener) {
			foreach ($this->listeners[$method] as $k => $candidateListener) {
				if ($candidateListener === $listener) {
					unset($this->listeners[$method][$k]);
					break;
				}
			}

			if (empty($this->listeners[$method])) {
				unset($this->listeners[$method]);
			}
		};

		return new class($callback) implements SubscriptionInterface
		{
			/** @var callable */
			private $callback;

			public function __construct(callable $callable)
			{
				$this->callback = $callable;
			}

			public function cancel(): void
			{
				$callback = $this->callback;
				$callback();
			}
		};
	}

}
