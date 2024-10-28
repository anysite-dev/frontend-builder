@props(['blocks' => []])

@foreach ($blocks as $blockData)
	@php
		$pageBlock = \AnysiteDev\FrontendBuilder\Facades\FrontendBuilder::getBlockFromName($blockData['type'])
	@endphp

	@isset($pageBlock)
		<x-dynamic-component
				:component="$pageBlock::getComponent()"
				:attributes="new \Illuminate\View\ComponentAttributeBag($pageBlock::mutateData($blockData['data']))"
		/>
	@endisset
@endforeach
