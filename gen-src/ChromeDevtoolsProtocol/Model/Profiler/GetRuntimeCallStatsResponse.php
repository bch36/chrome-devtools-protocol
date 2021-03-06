<?php

namespace ChromeDevtoolsProtocol\Model\Profiler;

/**
 * Response to Profiler.getRuntimeCallStats command.
 *
 * @generated This file has been auto-generated, do not edit.
 *
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 */
final class GetRuntimeCallStatsResponse implements \JsonSerializable
{
	/**
	 * Collected runtime call counter information.
	 *
	 * @var RuntimeCallCounterInfo[]
	 */
	public $result;


	public static function fromJson($data)
	{
		$instance = new static();
		if (isset($data->result)) {
			$instance->result = [];
			foreach ($data->result as $item) {
				$instance->result[] = RuntimeCallCounterInfo::fromJson($item);
			}
		}
		return $instance;
	}


	public function jsonSerialize()
	{
		$data = new \stdClass();
		if ($this->result !== null) {
			$data->result = [];
			foreach ($this->result as $item) {
				$data->result[] = $item->jsonSerialize();
			}
		}
		return $data;
	}
}
