<?php
/**
 * Preloader template
 */
?>
<style>
	.jet_preloader {
		position: relative;
		margin: 50px auto;
		width: 50px;
		height: 50px;
	}

	.jet_preloader svg {
		margin: auto;
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 100%;
		transform-origin: center center;
		animation: rotate 2s linear infinite;
	}

	.jet_preloader circle {
		stroke-dasharray: 1, 200;
		stroke-dashoffset: 0;
		stroke-linecap: round;
		stroke: #80BDDC;
		animation: dash 1.5s ease-in-out infinite;
	}

	@keyframes rotate {
		100% {
			transform: rotate(360deg);
		}
	}

	@keyframes dash {
		0% {
			stroke-dasharray: 1, 200;
			stroke-dashoffset: 0;
		}
		50% {
			stroke-dasharray: 89, 200;
			stroke-dashoffset: -35px;
		}
		100% {
			stroke-dasharray: 89, 200;
			stroke-dashoffset: -124px;
		}
	}
</style>
<div class="jet_preloader">
	<svg viewBox="25 25 50 50">
		<circle cx="50"
				cy="50"
				r="20"
				fill="none"
				stroke-width="4"
				stroke-miterlimit="10" />
	</svg>
</div>
