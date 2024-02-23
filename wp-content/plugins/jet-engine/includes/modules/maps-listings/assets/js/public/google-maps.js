function JetGMInfoBox(opt_opts) {

	opt_opts = opt_opts || {};

	google.maps.OverlayView.apply(this, arguments);

	// Standard options (in common with google.maps.InfoWindow):
	this.content_ = opt_opts.content || "";
	this.disableAutoPan_ = opt_opts.disableAutoPan || false;
	this.maxWidth_ = opt_opts.maxWidth || 0;
	this.pixelOffset_ = opt_opts.pixelOffset || new google.maps.Size(0, 0);
	this.position_ = opt_opts.position || new google.maps.LatLng(0, 0);
	this.zIndex_ = opt_opts.zIndex || null;

	// Additional options (unique to JetGMInfoBox):
	this.boxClass_ = opt_opts.boxClass || "infoBox";
	this.boxStyle_ = opt_opts.boxStyle || {};
	this.closeBoxMargin_ = opt_opts.closeBoxMargin || "2px";
	this.closeBoxURL_ = opt_opts.closeBoxURL || "//www.google.com/intl/en_us/mapfiles/close.gif";
	if (opt_opts.closeBoxURL === "") {
		this.closeBoxURL_ = "";
	}
	this.closeBoxTitle_ = opt_opts.closeBoxTitle || " Close ";
	this.infoBoxClearance_ = opt_opts.infoBoxClearance || new google.maps.Size(1, 1);

	if (typeof opt_opts.visible === "undefined") {
		if (typeof opt_opts.isHidden === "undefined") {
			opt_opts.visible = true;
		} else {
			opt_opts.visible = !opt_opts.isHidden;
		}
	}
	this.isHidden_ = !opt_opts.visible;

	this.alignBottom_ = opt_opts.alignBottom || false;
	this.pane_ = opt_opts.pane || "floatPane";
	this.enableEventPropagation_ = opt_opts.enableEventPropagation || false;

	this.div_ = null;
	this.closeListener_ = null;
	this.moveListener_ = null;
	this.contextListener_ = null;
	this.eventListeners_ = null;
	this.fixedWidthSet_ = null;

}

/* JetGMInfoBox extends OverlayView in the Google Maps API v3.
 */
JetGMInfoBox.prototype = new google.maps.OverlayView();

JetGMInfoBox.prototype.addEventListener = function ( target, type, handler, useCapture ) {

	useCapture = useCapture || false;

	target.addEventListener( type, handler, useCapture );

	return {
		target: target,
		type: type,
		handler: handler,
		useCapture: useCapture,
	}
}

JetGMInfoBox.prototype.removeEventListener = function ( listener ) {
	listener.target.removeEventListener( listener.type, listener.handler, listener.useCapture );
}

/**
 * Creates the DIV representing the JetGMInfoBox.
 * @private
 */
JetGMInfoBox.prototype.createJetGMInfoBoxDiv_ = function () {

	var i;
	var events;
	var bw;
	var me = this;

	// This handler prevents an event in the JetGMInfoBox from being passed on to the map.
	//
	var cancelHandler = function (e) {
		var hasListingOverlay = 'click' === e.type && e.currentTarget.querySelector('.jet-engine-listing-overlay-wrap');

		if ( hasListingOverlay ) {
			return;
		}

		e.cancelBubble = true;
		if (e.stopPropagation) {
			e.stopPropagation();
		}
	};

	// This handler ignores the current event in the JetGMInfoBox and conditionally prevents
	// the event from being passed on to the map. It is used for the contextmenu event.
	//
	var ignoreHandler = function (e) {

		e.returnValue = false;

		if (e.preventDefault) {

			e.preventDefault();
		}

		if (!me.enableEventPropagation_) {

			cancelHandler(e);
		}
	};

	if (!this.div_) {

		this.div_ = document.createElement("div");

		this.setBoxStyle_();

		if (typeof this.content_.nodeType === "undefined") {
			this.div_.innerHTML = this.getCloseBoxImg_() + this.content_;
		} else {
			this.div_.innerHTML = this.getCloseBoxImg_();
			this.div_.appendChild(this.content_);
		}

		// Add the JetGMInfoBox DIV to the DOM
		this.getPanes()[this.pane_].appendChild(this.div_);

		this.addClickHandler_();

		if (this.div_.style.width) {

			this.fixedWidthSet_ = true;

		} else {


			if ( this.maxWidth_ !== 0 ) {

				this.div_.style.width = this.maxWidth_ + 'px';
				this.fixedWidthSet_ = true;

			} else { // The following code is needed to overcome problems with MSIE

				bw = this.getBoxWidths_();

				this.div_.style.width = (this.div_.offsetWidth - bw.left - bw.right) + "px";
				this.fixedWidthSet_ = false;
			}
		}

		this.panBox_(this.disableAutoPan_);

		if (!this.enableEventPropagation_) {

			this.eventListeners_ = [];

			// Cancel event propagation.
			//
			// Note: mousemove not included (to resolve Issue 152)
			events = ["mousedown", "mouseover", "mouseout", "mouseup",
				"click", "dblclick", "touchstart", "touchend", "touchmove"];

			for (i = 0; i < events.length; i++) {

				this.eventListeners_.push(this.addEventListener(this.div_, events[i], cancelHandler));
			}

			// Workaround for Google bug that causes the cursor to change to a pointer
			// when the mouse moves over a marker underneath JetGMInfoBox.
			this.eventListeners_.push(this.addEventListener(this.div_, "mouseover", function (e) {
				this.style.cursor = "default";
			}));
		}

		this.contextListener_ = this.addEventListener(this.div_, "contextmenu", ignoreHandler);

		/**
		 * This event is fired when the DIV containing the JetGMInfoBox's content is attached to the DOM.
		 * @name JetGMInfoBox#domready
		 * @event
		 */
		google.maps.event.trigger(this, "domready");
	}
};

/**
 * Returns the HTML <IMG> tag for the close box.
 * @private
 */
JetGMInfoBox.prototype.getCloseBoxImg_ = function () {

	var img = "";

	if (this.closeBoxURL_ !== "") {

		img  = "<img";
		img += " src='" + this.closeBoxURL_ + "'";
		img += " align=right"; // Do this because Opera chokes on style='float: right;'
		img += " title='" + this.closeBoxTitle_ + "'";
		img += " class='jet-map-close'";
		img += " style='";
		img += " margin: " + this.closeBoxMargin_ + ";";
		img += "'>";
	}

	return img;
};

/**
 * Adds the click handler to the JetGMInfoBox close box.
 * @private
 */
JetGMInfoBox.prototype.addClickHandler_ = function () {

	var closeBox;

	if (this.closeBoxURL_ !== "") {

		closeBox = this.div_.firstChild;
		this.closeListener_ = this.addEventListener(closeBox, "click", this.getCloseClickHandler_());

	} else {

		this.closeListener_ = null;
	}
};

/**
 * Returns the function to call when the user clicks the close box of an JetGMInfoBox.
 * @private
 */
JetGMInfoBox.prototype.getCloseClickHandler_ = function () {

	var me = this;

	return function (e) {

		// 1.0.3 fix: Always prevent propagation of a close box click to the map:
		e.cancelBubble = true;

		if (e.stopPropagation) {

			e.stopPropagation();
		}

		/**
		 * This event is fired when the JetGMInfoBox's close box is clicked.
		 * @name JetGMInfoBox#closeclick
		 * @event
		 */
		google.maps.event.trigger(me, "closeclick");

		me.close();
	};
};

/**
 * Pans the map so that the JetGMInfoBox appears entirely within the map's visible area.
 * @private
 */
JetGMInfoBox.prototype.panBox_ = function (disablePan) {

	var map;
	var bounds;
	var xOffset = 0, yOffset = 0;

	if (!disablePan) {

		map = this.getMap();

		if (map instanceof google.maps.Map) { // Only pan if attached to map, not panorama

			if (!map.getBounds().contains(this.position_)) {
				// Marker not in visible area of map, so set center
				// of map to the marker position first.
				map.setCenter(this.position_);
			}

			var iwOffsetX = this.pixelOffset_.width;
			var iwOffsetY = this.pixelOffset_.height;
			var iwWidth = this.div_.offsetWidth;
			var iwHeight = this.div_.offsetHeight;
			var padX = this.infoBoxClearance_.width;
			var padY = this.infoBoxClearance_.height;

			if (map.panToBounds.length == 2) {
				// Using projection.fromLatLngToContainerPixel to compute the infowindow position
				// does not work correctly anymore for JS Maps API v3.32 and above if there is a
				// previous synchronous call that causes the map to animate (e.g. setCenter when
				// the position is not within bounds). Hence, we are using panToBounds with
				// padding instead, which works synchronously.
				var padding = {left: 0, right: 0, top: 0, bottom: 0};
				padding.left = -iwOffsetX + padX;
				padding.right = iwOffsetX + iwWidth + padX;
				if (this.alignBottom_) {
					padding.top = -iwOffsetY + padY + iwHeight;
					padding.bottom = iwOffsetY + padY;
				} else {
					padding.top = -iwOffsetY + padY;
					padding.bottom = iwOffsetY + iwHeight + padY;
				}
				map.panToBounds(new google.maps.LatLngBounds(this.position_), padding);
			} else {
				var mapDiv = map.getDiv();
				var mapWidth = mapDiv.offsetWidth;
				var mapHeight = mapDiv.offsetHeight;
				var pixPosition = this.getProjection().fromLatLngToContainerPixel(this.position_);

				if (pixPosition.x < (-iwOffsetX + padX)) {
					xOffset = pixPosition.x + iwOffsetX - padX;
				} else if ((pixPosition.x + iwWidth + iwOffsetX + padX) > mapWidth) {
					xOffset = pixPosition.x + iwWidth + iwOffsetX + padX - mapWidth;
				}
				if (this.alignBottom_) {
					if (pixPosition.y < (-iwOffsetY + padY + iwHeight)) {
						yOffset = pixPosition.y + iwOffsetY - padY - iwHeight;
					} else if ((pixPosition.y + iwOffsetY + padY) > mapHeight) {
						yOffset = pixPosition.y + iwOffsetY + padY - mapHeight;
					}
				} else {
					if (pixPosition.y < (-iwOffsetY + padY)) {
						yOffset = pixPosition.y + iwOffsetY - padY;
					} else if ((pixPosition.y + iwHeight + iwOffsetY + padY) > mapHeight) {
						yOffset = pixPosition.y + iwHeight + iwOffsetY + padY - mapHeight;
					}
				}

				if (!(xOffset === 0 && yOffset === 0)) {

					// Move the map to the shifted center.
					//
					var c = map.getCenter();
					map.panBy(xOffset, yOffset);
				}
			}
		}
	}
};

/**
 * Sets the style of the JetGMInfoBox by setting the style sheet and applying
 * other specific styles requested.
 * @private
 */
JetGMInfoBox.prototype.setBoxStyle_ = function () {

	var i, boxStyle;

	if (this.div_) {

		// Apply style values from the style sheet defined in the boxClass parameter:
		this.div_.className = this.boxClass_;

		// Clear existing inline style values:
		this.div_.style.cssText = "";

		// Apply style values defined in the boxStyle parameter:
		boxStyle = this.boxStyle_;
		for (i in boxStyle) {

			if (boxStyle.hasOwnProperty(i)) {

				this.div_.style[i] = boxStyle[i];
			}
		}

		// Fix for iOS disappearing JetGMInfoBox problem.
		// See http://stackoverflow.com/questions/9229535/google-maps-markers-disappear-at-certain-zoom-level-only-on-iphone-ipad
		// Required: use "matrix" technique to specify transforms in order to avoid this bug.
		if ((typeof this.div_.style.WebkitTransform === "undefined") || (this.div_.style.WebkitTransform.indexOf("translateZ") === -1 && this.div_.style.WebkitTransform.indexOf("matrix") === -1)) {

			this.div_.style.WebkitTransform = "translateZ(0)";
		}

		// Fix up opacity style for benefit of MSIE:
		//
		if (typeof this.div_.style.opacity !== "undefined" && this.div_.style.opacity !== "") {
			// See http://www.quirksmode.org/css/opacity.html
			this.div_.style.MsFilter = "\"progid:DXImageTransform.Microsoft.Alpha(Opacity=" + (this.div_.style.opacity * 100) + ")\"";
			this.div_.style.filter = "alpha(opacity=" + (this.div_.style.opacity * 100) + ")";
		}

		// Apply required styles:
		//
		this.div_.style.position = "absolute";
		this.div_.style.visibility = 'hidden';
		if (this.zIndex_ !== null) {

			this.div_.style.zIndex = this.zIndex_;
		}
	}
};

/**
 * Get the widths of the borders of the JetGMInfoBox.
 * @private
 * @return {Object} widths object (top, bottom left, right)
 */
JetGMInfoBox.prototype.getBoxWidths_ = function () {

	var computedStyle;
	var bw = {top: 0, bottom: 0, left: 0, right: 0};
	var box = this.div_;

	if (document.defaultView && document.defaultView.getComputedStyle) {

		computedStyle = box.ownerDocument.defaultView.getComputedStyle(box, "");

		if (computedStyle) {

			// The computed styles are always in pixel units (good!)
			bw.top = parseInt(computedStyle.borderTopWidth, 10) || 0;
			bw.bottom = parseInt(computedStyle.borderBottomWidth, 10) || 0;
			bw.left = parseInt(computedStyle.borderLeftWidth, 10) || 0;
			bw.right = parseInt(computedStyle.borderRightWidth, 10) || 0;
		}

	} else if (document.documentElement.currentStyle) { // MSIE

		if (box.currentStyle) {

			// The current styles may not be in pixel units, but assume they are (bad!)
			bw.top = parseInt(box.currentStyle.borderTopWidth, 10) || 0;
			bw.bottom = parseInt(box.currentStyle.borderBottomWidth, 10) || 0;
			bw.left = parseInt(box.currentStyle.borderLeftWidth, 10) || 0;
			bw.right = parseInt(box.currentStyle.borderRightWidth, 10) || 0;
		}
	}

	return bw;
};

/**
 * Invoked when <tt>close</tt> is called. Do not call it directly.
 */
JetGMInfoBox.prototype.onRemove = function () {

	if (this.div_) {

		this.div_.parentNode.removeChild(this.div_);
		this.div_ = null;
	}
};

/**
 * Draws the JetGMInfoBox based on the current map projection and zoom level.
 */
JetGMInfoBox.prototype.draw = function () {

	this.createJetGMInfoBoxDiv_();

	var pixPosition = this.getProjection().fromLatLngToDivPixel(this.position_);

	this.div_.style.left = (pixPosition.x + this.pixelOffset_.width) + "px";

	if (this.alignBottom_) {
		this.div_.style.bottom = -(pixPosition.y + this.pixelOffset_.height) + "px";
	} else {
		this.div_.style.top = (pixPosition.y + this.pixelOffset_.height) + "px";
	}

	if (this.isHidden_) {

		this.div_.style.visibility = "hidden";

	} else {

		this.div_.style.visibility = "visible";
	}
};

/**
 * Sets the options for the JetGMInfoBox. Note that changes to the <tt>maxWidth</tt>,
 *  <tt>closeBoxMargin</tt>, <tt>closeBoxTitle</tt>, <tt>closeBoxURL</tt>, and
 *  <tt>enableEventPropagation</tt> properties have no affect until the current
 *  JetGMInfoBox is <tt>close</tt>d and a new one is <tt>open</tt>ed.
 * @param {JetGMInfoBoxOptions} opt_opts
 */
JetGMInfoBox.prototype.setOptions = function (opt_opts) {
	if (typeof opt_opts.boxClass !== "undefined") { // Must be first

		this.boxClass_ = opt_opts.boxClass;
		this.setBoxStyle_();
	}
	if (typeof opt_opts.boxStyle !== "undefined") { // Must be second

		this.boxStyle_ = opt_opts.boxStyle;
		this.setBoxStyle_();
	}
	if (typeof opt_opts.content !== "undefined") {

		this.setContent(opt_opts.content);
	}
	if (typeof opt_opts.disableAutoPan !== "undefined") {

		this.disableAutoPan_ = opt_opts.disableAutoPan;
	}
	if (typeof opt_opts.maxWidth !== "undefined") {

		this.maxWidth_ = opt_opts.maxWidth;
	}
	if (typeof opt_opts.pixelOffset !== "undefined") {

		this.pixelOffset_ = opt_opts.pixelOffset;
	}
	if (typeof opt_opts.alignBottom !== "undefined") {

		this.alignBottom_ = opt_opts.alignBottom;
	}
	if (typeof opt_opts.position !== "undefined") {

		this.setPosition(opt_opts.position);
	}
	if (typeof opt_opts.zIndex !== "undefined") {

		this.setZIndex(opt_opts.zIndex);
	}
	if (typeof opt_opts.closeBoxMargin !== "undefined") {

		this.closeBoxMargin_ = opt_opts.closeBoxMargin;
	}
	if (typeof opt_opts.closeBoxURL !== "undefined") {

		this.closeBoxURL_ = opt_opts.closeBoxURL;
	}
	if (typeof opt_opts.closeBoxTitle !== "undefined") {

		this.closeBoxTitle_ = opt_opts.closeBoxTitle;
	}
	if (typeof opt_opts.infoBoxClearance !== "undefined") {

		this.infoBoxClearance_ = opt_opts.infoBoxClearance;
	}
	if (typeof opt_opts.isHidden !== "undefined") {

		this.isHidden_ = opt_opts.isHidden;
	}
	if (typeof opt_opts.visible !== "undefined") {

		this.isHidden_ = !opt_opts.visible;
	}
	if (typeof opt_opts.enableEventPropagation !== "undefined") {

		this.enableEventPropagation_ = opt_opts.enableEventPropagation;
	}

	if (this.div_) {

		this.draw();
	}
};

JetGMInfoBox.prototype.contentIsSet = function () {
	return "" !== this.content_;
};

/**
 * Sets the content of the JetGMInfoBox.
 *  The content can be plain text or an HTML DOM node.
 * @param {string|Node} content
 */
JetGMInfoBox.prototype.setContent = function (content) {
	// Convert a content to an HTMLElement to store the HTML manipulation in a popup
	if ( typeof content.nodeType === 'undefined' ) {
		let contentHtml = document.createElement( 'div' );
		contentHtml.innerHTML = content;
		content = contentHtml;
	}

	this.content_ = content;

	if (this.div_) {

		if (this.closeListener_) {

			this.removeEventListener(this.closeListener_);
			this.closeListener_ = null;
		}

		// Odd code required to make things work with MSIE.
		//
		if (!this.fixedWidthSet_) {

			this.div_.style.width = "";
		}

		if (typeof content.nodeType === "undefined") {
			this.div_.innerHTML = this.getCloseBoxImg_() + content;
		} else {
			this.div_.innerHTML = this.getCloseBoxImg_();
			this.div_.appendChild(content);
		}

		// Perverse code required to make things work with MSIE.
		// (Ensures the close box does, in fact, float to the right.)
		//
		if (!this.fixedWidthSet_) {
			this.div_.style.width = this.div_.offsetWidth + "px";
			if (typeof content.nodeType === "undefined") {
				this.div_.innerHTML = this.getCloseBoxImg_() + content;
			} else {
				this.div_.innerHTML = this.getCloseBoxImg_();
				this.div_.appendChild(content);
			}
		}

		this.addClickHandler_();
	}

	/**
	 * This event is fired when the content of the JetGMInfoBox changes.
	 * @name JetGMInfoBox#content_changed
	 * @event
	 */
	google.maps.event.trigger(this, "content_changed");
};

/**
 * Sets the geographic location of the JetGMInfoBox.
 * @param {LatLng} latlng
 */
JetGMInfoBox.prototype.setPosition = function (latlng) {

	this.position_ = latlng;

	if (this.div_) {

		this.draw();
	}

	/**
	 * This event is fired when the position of the JetGMInfoBox changes.
	 * @name JetGMInfoBox#position_changed
	 * @event
	 */
	google.maps.event.trigger(this, "position_changed");
};

/**
 * Sets the zIndex style for the JetGMInfoBox.
 * @param {number} index
 */
JetGMInfoBox.prototype.setZIndex = function (index) {

	this.zIndex_ = index;

	if (this.div_) {

		this.div_.style.zIndex = index;
	}

	/**
	 * This event is fired when the zIndex of the JetGMInfoBox changes.
	 * @name JetGMInfoBox#zindex_changed
	 * @event
	 */
	google.maps.event.trigger(this, "zindex_changed");
};

/**
 * Sets the visibility of the JetGMInfoBox.
 * @param {boolean} isVisible
 */
JetGMInfoBox.prototype.setVisible = function (isVisible) {

	this.isHidden_ = !isVisible;
	if (this.div_) {
		this.div_.style.visibility = (this.isHidden_ ? "hidden" : "visible");
	}
};

/**
 * Returns the content of the JetGMInfoBox.
 * @returns {string}
 */
JetGMInfoBox.prototype.getContent = function () {

	return this.content_;
};

/**
 * Returns the geographic location of the JetGMInfoBox.
 * @returns {LatLng}
 */
JetGMInfoBox.prototype.getPosition = function () {

	return this.position_;
};

/**
 * Returns the zIndex for the JetGMInfoBox.
 * @returns {number}
 */
JetGMInfoBox.prototype.getZIndex = function () {

	return this.zIndex_;
};

/**
 * Returns a flag indicating whether the JetGMInfoBox is visible.
 * @returns {boolean}
 */
JetGMInfoBox.prototype.getVisible = function () {

	var isVisible;

	if ((typeof this.getMap() === "undefined") || (this.getMap() === null)) {
		isVisible = false;
	} else {
		isVisible = !this.isHidden_;
	}
	return isVisible;
};

/**
 * Returns the width of the JetGMInfoBox in pixels.
 * @returns {number}
 */
JetGMInfoBox.prototype.getWidth = function () {
	var width = null;

	if (this.div_) {
		width = this.div_.offsetWidth;
	}

	return width;
};

/**
 * Returns the height of the JetGMInfoBox in pixels.
 * @returns {number}
 */
JetGMInfoBox.prototype.getHeight = function () {
	var height = null;

	if (this.div_) {
		height = this.div_.offsetHeight;
	}

	return height;
};

/**
 * Shows the JetGMInfoBox. [Deprecated; use <tt>setVisible</tt> instead.]
 */
JetGMInfoBox.prototype.show = function () {

	this.isHidden_ = false;
	if (this.div_) {
		this.div_.style.visibility = "visible";
	}
};

/**
 * Hides the JetGMInfoBox. [Deprecated; use <tt>setVisible</tt> instead.]
 */
JetGMInfoBox.prototype.hide = function () {

	this.isHidden_ = true;
	if (this.div_) {
		this.div_.style.visibility = "hidden";
	}
};

/**
 * Adds the JetGMInfoBox to the specified map or Street View panorama. If <tt>anchor</tt>
 *  (usually a <tt>google.maps.Marker</tt>) is specified, the position
 *  of the JetGMInfoBox is set to the position of the <tt>anchor</tt>. If the
 *  anchor is dragged to a new location, the JetGMInfoBox moves as well.
 * @param {Map|StreetViewPanorama} map
 * @param {MVCObject} [anchor]
 */
JetGMInfoBox.prototype.open = function (map, anchor) {

	var me = this;

	if (anchor) {

		this.setPosition(anchor.getPosition()); // BUG FIX 2/17/2018: needed for v3.32
		this.moveListener_ = google.maps.event.addListener(anchor, "position_changed", function () {
			me.setPosition(this.getPosition());
		});
	}

	this.setMap(map);

	if ( this.div_ ) {

		this.panBox_(this.disableAutoPan_); // BUG FIX 2/17/2018: add missing parameter

	}
};

/**
 * Removes the JetGMInfoBox from the map.
 */
JetGMInfoBox.prototype.close = function () {

	var i;

	if (this.closeListener_) {

		this.removeEventListener(this.closeListener_);
		this.closeListener_ = null;
	}

	if (this.eventListeners_) {

		for (i = 0; i < this.eventListeners_.length; i++) {

			this.removeEventListener(this.eventListeners_[i]);
		}
		this.eventListeners_ = null;
	}

	if (this.moveListener_) {

		google.maps.event.removeListener(this.moveListener_);
		this.moveListener_ = null;
	}

	if (this.contextListener_) {

		this.removeEventListener(this.contextListener_);
		this.contextListener_ = null;
	}

	this.setMap(null);
};

// richmarker.js
// initial version - https://github.com/googlearchive/js-rich-marker
// new version - https://github.com/kaskad88/js-rich-marker // fixed deprecated notices
(function(){function t(t){var r=t||{};this.ready_=!1,this.dragging_=!1,null==t.visible&&(t.visible=!0),null==t.shadow&&(t.shadow="7px -3px 5px rgba(88,88,88,0.7)"),null==t.anchor&&(t.anchor=e.BOTTOM),this.setValues(r)}t.prototype=new google.maps.OverlayView,window.RichMarker=t,t.prototype.getVisible=function(){return this.get("visible")},t.prototype.getVisible=t.prototype.getVisible,t.prototype.setVisible=function(t){this.set("visible",t)},t.prototype.setVisible=t.prototype.setVisible,t.prototype.visible_changed=function(){this.ready_&&(this.markerWrapper_.style.display=this.getVisible()?"":"none",this.draw())},t.prototype.visible_changed=t.prototype.visible_changed,t.prototype.setFlat=function(t){this.set("flat",!!t)},t.prototype.setFlat=t.prototype.setFlat,t.prototype.getFlat=function(){return this.get("flat")},t.prototype.getFlat=t.prototype.getFlat,t.prototype.getWidth=function(){return this.get("width")},t.prototype.getWidth=t.prototype.getWidth,t.prototype.getHeight=function(){return this.get("height")},t.prototype.getHeight=t.prototype.getHeight,t.prototype.setShadow=function(t){this.set("shadow",t),this.flat_changed()},t.prototype.setShadow=t.prototype.setShadow,t.prototype.getShadow=function(){return this.get("shadow")},t.prototype.getShadow=t.prototype.getShadow,t.prototype.flat_changed=function(){this.ready_&&(this.markerWrapper_.style.boxShadow=this.markerWrapper_.style.webkitBoxShadow=this.markerWrapper_.style.MozBoxShadow=this.getFlat()?"":this.getShadow())},t.prototype.flat_changed=t.prototype.flat_changed,t.prototype.setZIndex=function(t){this.set("zIndex",t)},t.prototype.setZIndex=t.prototype.setZIndex,t.prototype.getZIndex=function(){return this.get("zIndex")},t.prototype.getZIndex=t.prototype.getZIndex,t.prototype.zIndex_changed=function(){this.getZIndex()&&this.ready_&&(this.markerWrapper_.style.zIndex=this.getZIndex())},t.prototype.zIndex_changed=t.prototype.zIndex_changed,t.prototype.getDraggable=function(){return this.get("draggable")},t.prototype.getDraggable=t.prototype.getDraggable,t.prototype.setDraggable=function(t){this.set("draggable",!!t)},t.prototype.setDraggable=t.prototype.setDraggable,t.prototype.draggable_changed=function(){this.ready_&&(this.getDraggable()?this.addDragging_(this.markerWrapper_):this.removeDragListeners_())},t.prototype.draggable_changed=t.prototype.draggable_changed,t.prototype.getPosition=function(){return this.get("position")},t.prototype.getPosition=t.prototype.getPosition,t.prototype.setPosition=function(t){this.set("position",t)},t.prototype.setPosition=t.prototype.setPosition,t.prototype.position_changed=function(){this.draw()},t.prototype.position_changed=t.prototype.position_changed,t.prototype.getAnchor=function(){return this.get("anchor")},t.prototype.getAnchor=t.prototype.getAnchor,t.prototype.setAnchor=function(t){this.set("anchor",t)},t.prototype.setAnchor=t.prototype.setAnchor,t.prototype.anchor_changed=function(){this.draw()},t.prototype.anchor_changed=t.prototype.anchor_changed,t.prototype.htmlToDocumentFragment_=function(t){var e=document.createElement("DIV");if(e.innerHTML=t,1==e.childNodes.length)return e.removeChild(e.firstChild);for(var r=document.createDocumentFragment();e.firstChild;)r.appendChild(e.firstChild);return r},t.prototype.removeChildren_=function(t){if(t)for(var e;e=t.firstChild;)t.removeChild(e)},t.prototype.setContent=function(t){this.set("content",t)},t.prototype.setContent=t.prototype.setContent,t.prototype.getContent=function(){return this.get("content")},t.prototype.getContent=t.prototype.getContent,t.prototype.addEventListener=function(t,e,r,o){return o=o||!1,t.addEventListener(e,r,o),{target:t,type:e,handler:r,useCapture:o}},t.prototype.removeEventListener=function(t){t.target.removeEventListener(t.type,t.handler,t.useCapture)},t.prototype.content_changed=function(){if(this.markerContent_){this.removeChildren_(this.markerContent_);var t=this.getContent();if(t){"string"==typeof t&&(t=t.replace(/^\s*([\S\s]*)\b\s*$/,"$1"),t=this.htmlToDocumentFragment_(t)),this.markerContent_.appendChild(t);for(var e,r=this,o=this.markerContent_.getElementsByTagName("IMG"),i=0;e=o[i];i++)this.addEventListener(e,"mousedown",(function(t){r.getDraggable()&&(t.preventDefault&&t.preventDefault(),t.returnValue=!1,console.log("image mousedown"))})),this.addEventListener(e,"load",(function(){r.draw(),console.log("image load")}));google.maps.event.trigger(this,"domready")}this.ready_&&this.draw()}},t.prototype.content_changed=t.prototype.content_changed,t.prototype.setCursor_=function(t){if(this.ready_){var e="";-1!==navigator.userAgent.indexOf("Gecko/")?("dragging"==t&&(e="-moz-grabbing"),"dragready"==t&&(e="-moz-grab"),"draggable"==t&&(e="pointer")):("dragging"!=t&&"dragready"!=t||(e="move"),"draggable"==t&&(e="pointer")),this.markerWrapper_.style.cursor!=e&&(this.markerWrapper_.style.cursor=e)}},t.prototype.startDrag=function(t){if(this.getDraggable()&&!this.dragging_){this.dragging_=!0;var e=this.getMap();this.mapDraggable_=e.get("draggable"),e.set("draggable",!1),this.mouseX_=t.clientX,this.mouseY_=t.clientY,this.setCursor_("dragready"),this.markerWrapper_.style.MozUserSelect="none",this.markerWrapper_.style.KhtmlUserSelect="none",this.markerWrapper_.style.WebkitUserSelect="none",this.markerWrapper_.unselectable="on",this.markerWrapper_.onselectstart=function(){return!1},this.addDraggingListeners_(),google.maps.event.trigger(this,"dragstart")}},t.prototype.stopDrag=function(){this.getDraggable()&&this.dragging_&&(this.dragging_=!1,this.getMap().set("draggable",this.mapDraggable_),this.mouseX_=this.mouseY_=this.mapDraggable_=null,this.markerWrapper_.style.MozUserSelect="",this.markerWrapper_.style.KhtmlUserSelect="",this.markerWrapper_.style.WebkitUserSelect="",this.markerWrapper_.unselectable="off",this.markerWrapper_.onselectstart=function(){},this.removeDraggingListeners_(),this.setCursor_("draggable"),google.maps.event.trigger(this,"dragend"),this.draw())},t.prototype.drag=function(t){if(this.getDraggable()&&this.dragging_){var e=this.mouseX_-t.clientX,r=this.mouseY_-t.clientY;this.mouseX_=t.clientX,this.mouseY_=t.clientY;var o=parseInt(this.markerWrapper_.style.left,10)-e,i=parseInt(this.markerWrapper_.style.top,10)-r;this.markerWrapper_.style.left=o+"px",this.markerWrapper_.style.top=i+"px";var s=this.getOffset_(),n=new google.maps.Point(o-s.width,i-s.height),a=this.getProjection();this.setPosition(a.fromDivPixelToLatLng(n)),this.setCursor_("dragging"),google.maps.event.trigger(this,"drag")}else this.stopDrag()},t.prototype.removeDragListeners_=function(){this.draggableListener_&&(this.removeEventListener(this.draggableListener_),delete this.draggableListener_),this.setCursor_("")},t.prototype.addDragging_=function(t){if(t){var e=this;this.draggableListener_=this.addEventListener(t,"mousedown",(function(t){e.startDrag(t),console.log("node mousedown")})),this.setCursor_("draggable")}},t.prototype.addDraggingListeners_=function(){var t=this;this.markerWrapper_.setCapture?(this.markerWrapper_.setCapture(!0),this.draggingListeners_=[this.addEventListener(this.markerWrapper_,"mousemove",(function(e){t.drag(e)}),!0),this.addEventListener(this.markerWrapper_,"mouseup",(function(){t.stopDrag(),t.markerWrapper_.releaseCapture()}),!0)]):this.draggingListeners_=[this.addEventListener(window,"mousemove",(function(e){t.drag(e)}),!0),this.addEventListener(window,"mouseup",(function(){t.stopDrag()}),!0)]},t.prototype.removeDraggingListeners_=function(){if(this.draggingListeners_){for(var t,e=0;t=this.draggingListeners_[e];e++)this.removeEventListener(t);this.draggingListeners_.length=0}},t.prototype.getOffset_=function(){var t=this.getAnchor();if("object"==typeof t)return t;var r=new google.maps.Size(0,0);if(!this.markerContent_)return r;var o=this.markerContent_.offsetWidth,i=this.markerContent_.offsetHeight;switch(t){case e.TOP_LEFT:break;case e.TOP:r.width=-o/2;break;case e.TOP_RIGHT:r.width=-o;break;case e.LEFT:r.height=-i/2;break;case e.MIDDLE:r.width=-o/2,r.height=-i/2;break;case e.RIGHT:r.width=-o,r.height=-i/2;break;case e.BOTTOM_LEFT:r.height=-i;break;case e.BOTTOM:r.width=-o/2,r.height=-i;break;case e.BOTTOM_RIGHT:r.width=-o,r.height=-i}return r},t.prototype.onAdd=function(){if(this.markerWrapper_||(this.markerWrapper_=document.createElement("DIV"),this.markerWrapper_.style.position="absolute"),this.getZIndex()&&(this.markerWrapper_.style.zIndex=this.getZIndex()),this.markerWrapper_.style.display=this.getVisible()?"":"none",!this.markerContent_){this.markerContent_=document.createElement("DIV"),this.markerWrapper_.appendChild(this.markerContent_);var t=this;this.addEventListener(this.markerContent_,"click",(function(e){google.maps.event.trigger(t,"click",e)})),this.addEventListener(this.markerContent_,"mouseover",(function(e){google.maps.event.trigger(t,"mouseover",e)})),this.addEventListener(this.markerContent_,"mouseout",(function(e){google.maps.event.trigger(t,"mouseout",e)}))}this.ready_=!0,this.content_changed(),this.flat_changed(),this.draggable_changed();var e=this.getPanes();e&&e.overlayMouseTarget.appendChild(this.markerWrapper_),google.maps.event.trigger(this,"ready")},t.prototype.onAdd=t.prototype.onAdd,t.prototype.draw=function(){if(this.ready_&&!this.dragging_){var t=this.getProjection();if(t){var e=this.get("position"),r=t.fromLatLngToDivPixel(e),o=this.getOffset_();this.markerWrapper_.style.top=r.y+o.height+"px",this.markerWrapper_.style.left=r.x+o.width+"px";var i=this.markerContent_.offsetHeight,s=this.markerContent_.offsetWidth;s!=this.get("width")&&this.set("width",s),i!=this.get("height")&&this.set("height",i)}}},t.prototype.draw=t.prototype.draw,t.prototype.onRemove=function(){this.markerWrapper_&&this.markerWrapper_.parentNode&&this.markerWrapper_.parentNode.removeChild(this.markerWrapper_),this.removeDragListeners_()},t.prototype.onRemove=t.prototype.onRemove;var e={TOP_LEFT:1,TOP:2,TOP_RIGHT:3,LEFT:4,MIDDLE:5,RIGHT:6,BOTTOM_LEFT:7,BOTTOM:8,BOTTOM_RIGHT:9};window.RichMarkerPosition=e;})();

window.JetEngineMapsProvider = function() {

	this.getId = function() {
		return 'google';
	}

	this.initMap = function( container, settings ) {

		settings = settings || {};
		
		if ( ! settings.mapTypeId ) {
			settings.mapTypeId = google.maps.MapTypeId.ROADMAP;
		}
		
		let map = new google.maps.Map( container, settings );
		
		return map;
	}

	this.initBounds = function() {
		return new google.maps.LatLngBounds();
	}

	this.fitMapBounds = function( data, callback ) {
		var self = this;

		data.map.fitBounds( data.bounds );

		var listener = google.maps.event.addListener( data.map, 'idle', function() {
			if ( ! data.marker.getMap() ) {
				self.fitMapToMarker( data.marker, data.markersClusterer );
			} else if ( callback ) {
				callback();
			}
			google.maps.event.removeListener( listener );
		} );
	}

	this.addMarker = function( data ) {
		data.position = new google.maps.LatLng( data.position.lat, data.position.lng );
		return new RichMarker( data );
	}

	this.removeMarker = function( marker ) {
		marker.setMap( null );
	}

	this.addPopup = function( data ) {
		return new JetGMInfoBox( {
			position: new google.maps.LatLng( data.position.lat, data.position.lng ),
			maxWidth: data.width,
			boxClass: "jet-map-box",
			zIndex: null,
			pixelOffset: new google.maps.Size( 0 - data.width / 2, 0 - data.offset ),
			alignBottom: true,
			infoBoxClearance: new google.maps.Size( 10, 10 ),
			pane: "floatPane",
			enableEventPropagation: false,
		} );
	}

	this.markerOnClick = function( map, data, callback ) {

		data = data || {};

		data.map = map;
		data.shadow = false;

		google.maps.event.addListener( map, "click", ( event ) => {

			data.position = {
				lat: event.latLng.lat(),
				lng: event.latLng.lng(),
			};

			if ( callback ) {
				callback( this.addMarker( data ) );
			}

		} );
	}

	this.closePopup = function( infoBox, callback ) {
		google.maps.event.addListener( infoBox, 'closeclick', callback );
	}

	this.openPopup = function( trigger, callback, infobox, map, openOn ) {
		google.maps.event.addListener( trigger, 'click', callback );

		if ( 'hover' === openOn ) {
			google.maps.event.addListener( trigger, 'mouseover', callback );
		}
	}

	this.triggerOpenPopup = function( trigger ) {
		google.maps.event.trigger( trigger, 'click' );
	}

	this.getMarkerPosition = function( marker, toJSON ) {
		toJSON = toJSON || false;

		if ( toJSON ) {
			return marker.position.toJSON();
		} else {
			return marker.position;
		}
		
	}

	this.getMarkerCluster = function( data ) {
		let options = {
			imagePath: data.clustererImg,
		};

		const optionsMap = {
			maxZoom: 'clusterMaxZoom',
			gridSize: 'clusterRadius',
		};

		for ( const [ optionKey, settingsKey ] of Object.entries( optionsMap ) ) {
			if ( undefined !== data[ settingsKey ] && '' !== data[ settingsKey ]  ) {
				options[ optionKey ] = data[ settingsKey ];
			}
		}

		return new MarkerClusterer(
			data.map,
			data.markers,
			options
		);
	}

	this.addMarkers = function( markerCluster, markers ) {
		markerCluster.addMarkers( markers );
	}

	this.removeMarkers = function( markerCluster, markers ) {
		markerCluster.removeMarkers( markers );
	}

	this.setCenterByPosition = function( data ) {
		data.map.setCenter( data.position );
		data.map.setZoom( data.zoom );
	}

	this.setAutoCenter = function( data ) {

		data.map.fitBounds( data.bounds );

		if ( data.settings.maxZoom ) {

			var listener = google.maps.event.addListener( data.map, 'idle', function() {

				if ( data.map.getZoom() > data.settings.maxZoom ) {
					data.map.setZoom( data.settings.maxZoom );
				}

				google.maps.event.removeListener( listener );

			} );
		}
	}

	this.getMarkerMap = function( marker ) {
		return marker.getMap();
	}

	this.fitMapToMarker = function( marker, markersClusterer, zoom ) {
		var cluster = this._findClusterByMarker( markersClusterer, marker ),
			bounds,
			map;

		if ( ! cluster ) {
			return;
		}

		map    = markersClusterer.getMap();
		bounds = cluster.getBounds();

		this.fitMapBounds( {
			map: map,
			bounds: bounds,
			marker: marker,
			markersClusterer: markersClusterer,
		}, () => {
			this.panTo( {
				map: map,
				position: this.getMarkerPosition( marker ),
				zoom: zoom
			} );
		} );
	};

	this.panTo = function( data ) {
		data.map.panTo( data.position );

		if ( data.zoom && data.zoom > data.map.getZoom() ) {
			data.map.setZoom( data.zoom );
		}
	}

	this._findClusterByMarker = function( markersClusterer, marker ) {
		var clusters = markersClusterer.getClusters(),
			result;

		if ( !clusters.length ) {
			return;
		}

		for ( var i = 0; i < clusters.length; i++ ) {
			var markers = clusters[i].getMarkers();

			for ( var j = 0; j < markers.length; j++ ) {

				if ( markers[j] === marker && markers.length > 1) {
					result = clusters[i];
					break;
				}
			}
		}

		return result;
	}

}
