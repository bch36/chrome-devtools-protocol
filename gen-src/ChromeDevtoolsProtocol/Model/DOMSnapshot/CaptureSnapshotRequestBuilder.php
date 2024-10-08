<?php

namespace ChromeDevtoolsProtocol\Model\DOMSnapshot;

use ChromeDevtoolsProtocol\Exception\BuilderException;

/**
 * @generated This file has been auto-generated, do not edit.
 *
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 */
final class CaptureSnapshotRequestBuilder
{
	private $computedStyles;
	private $includePaintOrder;
	private $includeDOMRects;
	private $includeBlendedBackgroundColors;
	private $includeTextColorOpacities;


	/**
	 * Validate non-optional parameters and return new instance.
	 */
	public function build(): CaptureSnapshotRequest
	{
		$instance = new CaptureSnapshotRequest();
		if ($this->computedStyles === null) {
			throw new BuilderException('Property [computedStyles] is required.');
		}
		$instance->computedStyles = $this->computedStyles;
		$instance->includePaintOrder = $this->includePaintOrder;
		$instance->includeDOMRects = $this->includeDOMRects;
		$instance->includeBlendedBackgroundColors = $this->includeBlendedBackgroundColors;
		$instance->includeTextColorOpacities = $this->includeTextColorOpacities;
		return $instance;
	}


	/**
	 * @param string[] $computedStyles
	 *
	 * @return self
	 */
	public function setComputedStyles($computedStyles): self
	{
		$this->computedStyles = $computedStyles;
		return $this;
	}


	/**
	 * @param bool|null $includePaintOrder
	 *
	 * @return self
	 */
	public function setIncludePaintOrder($includePaintOrder): self
	{
		$this->includePaintOrder = $includePaintOrder;
		return $this;
	}


	/**
	 * @param bool|null $includeDOMRects
	 *
	 * @return self
	 */
	public function setIncludeDOMRects($includeDOMRects): self
	{
		$this->includeDOMRects = $includeDOMRects;
		return $this;
	}


	/**
	 * @param bool|null $includeBlendedBackgroundColors
	 *
	 * @return self
	 */
	public function setIncludeBlendedBackgroundColors($includeBlendedBackgroundColors): self
	{
		$this->includeBlendedBackgroundColors = $includeBlendedBackgroundColors;
		return $this;
	}


	/**
	 * @param bool|null $includeTextColorOpacities
	 *
	 * @return self
	 */
	public function setIncludeTextColorOpacities($includeTextColorOpacities): self
	{
		$this->includeTextColorOpacities = $includeTextColorOpacities;
		return $this;
	}
}
