<?php

namespace ChromeDevtoolsProtocol\Model\Runtime;

use ChromeDevtoolsProtocol\Exception\BuilderException;

/**
 * @generated This file has been auto-generated, do not edit.
 *
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 */
final class AddBindingRequestBuilder
{
	private $name;
	private $executionContextId;
	private $executionContextName;


	/**
	 * Validate non-optional parameters and return new instance.
	 */
	public function build(): AddBindingRequest
	{
		$instance = new AddBindingRequest();
		if ($this->name === null) {
			throw new BuilderException('Property [name] is required.');
		}
		$instance->name = $this->name;
		$instance->executionContextId = $this->executionContextId;
		$instance->executionContextName = $this->executionContextName;
		return $instance;
	}


	/**
	 * @param string $name
	 *
	 * @return self
	 */
	public function setName($name): self
	{
		$this->name = $name;
		return $this;
	}


	/**
	 * @param int $executionContextId
	 *
	 * @return self
	 */
	public function setExecutionContextId($executionContextId): self
	{
		$this->executionContextId = $executionContextId;
		return $this;
	}


	/**
	 * @param string|null $executionContextName
	 *
	 * @return self
	 */
	public function setExecutionContextName($executionContextName): self
	{
		$this->executionContextName = $executionContextName;
		return $this;
	}
}
