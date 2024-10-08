<?php

namespace ChromeDevtoolsProtocol\Model\Debugger;

use ChromeDevtoolsProtocol\Exception\BuilderException;

/**
 * @generated This file has been auto-generated, do not edit.
 *
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 */
final class SetBlackboxPatternsRequestBuilder
{
	private $patterns;
	private $skipAnonymous;


	/**
	 * Validate non-optional parameters and return new instance.
	 */
	public function build(): SetBlackboxPatternsRequest
	{
		$instance = new SetBlackboxPatternsRequest();
		if ($this->patterns === null) {
			throw new BuilderException('Property [patterns] is required.');
		}
		$instance->patterns = $this->patterns;
		$instance->skipAnonymous = $this->skipAnonymous;
		return $instance;
	}


	/**
	 * @param string[] $patterns
	 *
	 * @return self
	 */
	public function setPatterns($patterns): self
	{
		$this->patterns = $patterns;
		return $this;
	}


	/**
	 * @param bool|null $skipAnonymous
	 *
	 * @return self
	 */
	public function setSkipAnonymous($skipAnonymous): self
	{
		$this->skipAnonymous = $skipAnonymous;
		return $this;
	}
}
