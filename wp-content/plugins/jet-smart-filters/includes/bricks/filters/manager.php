<?php

namespace Jet_Smart_Filters\Bricks_Views\Filters;

define( 'BRICKS_QUERY_LOOP_PROVIDER_ID', 'bricks-query-loop' );
define( 'BRICKS_QUERY_LOOP_PROVIDER_NAME', 'Bricks query loop' );

class Manager {

	public function __construct() {
		/**
		 * Register custom provider
		 */

		add_action( 'init', [ $this, 'add_control_to_elements' ], 40 );
		add_action( 'jet-smart-filters/providers/register', [ $this, 'register_provider_for_filters' ] );
		add_filter( 'jet-smart-filters/filters/localized-data', [ $this, 'add_script' ] );
		add_filter( 'jet-engine/query-builder/filters/allowed-providers', [ $this, 'add_provider_to_query_builder' ] );

	}

	public function register_provider_for_filters( $providers_manager ) {
		$providers_manager->register_provider(
			'\Jet_Smart_Filters\Bricks_Views\Filters\Provider', // Custom provider class name
			jet_smart_filters()->plugin_path( 'includes/bricks/filters/provider.php' ) // Path to file where this class defined
		);
	}

	public function add_control_to_elements() {

		// Only container, block and div element have query controls
		$elements = [ 'container', 'block', 'div' ];

		foreach ( $elements as $name ) {
			add_filter( "bricks/elements/{$name}/controls", [ $this, 'add_jet_smart_filters_controls' ], 40 );
		}
	}

	public function add_jet_smart_filters_controls( $controls ) {

		$jet_smart_filters_control['jsfb_is_filterable'] = [
			'tab'         => 'content',
			'label'       => esc_html__( 'Is filterable', 'jet-smart-filters' ),
			'type'        => 'checkbox',
			'required'    => [
				[ 'hasLoop', '=', true ],
			],
			'rerender'    => true,
			'description' => esc_html__( 'Please check this option if you will use with JetSmartFilters.', 'jet-smart-filters' ),
		];

		$jet_smart_filters_control['jsfb_query_id'] = [
			'tab'            => 'content',
			'label'          => esc_html__( 'Query ID for filters', 'jet-smart-filters' ),
			'type'           => 'text',
			'placeholder'    => esc_html__( 'Please enter query id.', 'jet-smart-filters' ),
			'hasDynamicData' => false,
			'required'       => [
				[ 'hasLoop', '=', true ],
				[ 'jsfb_is_filterable', '=', true ]
			],
			'rerender'       => true,
		];

		// Below 2 lines is just some php array functions to force my new control located after the query control
		$query_key_index = absint( array_search( 'query', array_keys( $controls ) ) );
		$new_controls    = array_slice( $controls, 0, $query_key_index + 1, true ) + $jet_smart_filters_control + array_slice( $controls, $query_key_index + 1, null, true );

		return $new_controls;
	}

	public function add_provider_to_query_builder( $providers ) {
		$providers[] = BRICKS_QUERY_LOOP_PROVIDER_ID;

		return $providers;
	}

	public function add_script( $data ) {

		wp_add_inline_script( 'jet-smart-filters', '
			const filtersStack = {};

			document.addEventListener( "jet-smart-filters/inited", () => {
				window.JetSmartFilters.events.subscribe( "ajaxFilters/start-loading", (provider, queryID) => {
					if ( "bricks-query-loop" === provider && filtersStack[queryID] ) {
						delete filtersStack[queryID];
					}
				} );
			} );
			
			window.JetSmartFilters.events.subscribe("ajaxFilters/updated", (provider, queryId, response) => {
				if ("bricks-query-loop" !== provider) {
					return;
				}

				let filterGroup = window.JetSmartFilters.filterGroups[provider + "/" + queryId];
				
				if (!filterGroup || !filterGroup.$provider.length) {
					return;
				}
				
				const {
					$provider: nodes,
					providerSelector
				} = filterGroup;
								
				const {
					rendered_content: renderedContent,
					element_id: elementId,
					loadMore,
					pagination,
					styles,
				} = response;
				
				const selector = `jsfb-query--${queryId}`;
				
				if (nodes[0].classList.contains(selector) && !filtersStack[queryId]) {
					filtersStack[queryId] = true;					
					let replaced = false;
					
					const replaceContent = () => {
						if (replaced) {
							return "";
						} else {
							replaced = true;
							return renderedContent;
						}
					}
					
					// Replace content
					if ( loadMore ) {
						jQuery(providerSelector).last().after(renderedContent);
					} else {
						jQuery(providerSelector).replaceWith(() => replaceContent());
					}
					
					// Remove the previous style element and Insert the new style element
					const {
						id: styleElementId,
						style: styleElement
					} = styles;
					const previousStyleElement = document.getElementById(styleElementId);
					
					if (previousStyleElement) {
						document.body.removeChild(previousStyleElement);
					}
					
					document.body.insertAdjacentHTML("beforeend", styleElement);
					
					// Initializing a plugin
					const filteredNodes = jQuery(providerSelector);
					
					window.JetPlugins && window.JetPlugins.init(filteredNodes.closest("*"));
					
					// Re-init Bricks scripts after filtering
					const bricksScripts = {
						".bricks-lightbox": bricksPhotoswipe,
						".brxe-accordion, .brxe-accordion-nested": bricksAccordion,
						".brxe-animated-typing": bricksAnimatedTyping,
						".brxe-audio": bricksAudio,
						".brxe-countdown": bricksCountdown,
						".brxe-counter": bricksCounter,
						".brxe-video": bricksVideo,
						".bricks-lazy-hidden": bricksLazyLoad,
						".brx-animated": bricksAnimation,
						".brxe-pie-chart": bricksPieChart,
						".brxe-progress-bar .bar span": bricksProgressBar,
						".brxe-form": bricksForm,
						".brx-query-trail": bricksInitQueryLoopInstances,
						"[data-interactions]": bricksInteractions,
						".brxe-alert svg": bricksAlertDismiss,
						".brxe-tabs, .brxe-tabs-nested": bricksTabs,
						".bricks-video-overlay, .bricks-video-overlay-icon, .bricks-video-preview-image": bricksVideoOverlayClickDetector,
						".bricks-background-video-wrapper": bricksBackgroundVideoInit,
						".brxe-toggle": bricksToggle,
						".brxe-offcanvas": bricksOffcanvas,
					};
											
					const contentWrapper = filteredNodes[0].parentNode;
					
					for (const key in bricksScripts) {
						const widget = contentWrapper.querySelector(key);
					
						if (widget && typeof bricksScripts[key] === "function" && bricksScripts[key]) {
					        bricksScripts[key](); // run function
					    }
					}
					
					// Re-init Bricks scripts when filtering children of nested elements
					const scriptsNestedElements = {
						".brxe-accordion-nested": {
							node: contentWrapper,
							func: bricksAccordionFn.run
						},
						".brxe-tabs-nested": {
							node: contentWrapper.parentNode,
							func: bricksTabsFn.run
						},
						".brxe-slider-nested.splide": {
							node: contentWrapper.parentNode.parentNode,
							func: bricksSplideFn.run
						},
					};
					
					const options = {
						forceReinit: true,
					};
					
					for (const key in scriptsNestedElements) {
						const {node, func} = scriptsNestedElements[key];
						if (node && node.matches(key)) {
							options.parentNode = node.parentNode;
							func(options);
						}
					}
															
					const interactions = document.querySelectorAll("[data-interactions]");
					
					// Manage the visibility of "Load More" buttons
					if (interactions.length) {
						interactions.forEach(el => {
							const {loadMoreQuery} = JSON.parse(el.dataset.interactions)[0];
							const {max_num_pages: maxPages, page} = pagination;
								
							if (elementId === loadMoreQuery) {
								el.style.display = page >= maxPages ? "none" : "";
							}
						});	
					}
				}
			});

		' );

		return $data;

	}
}