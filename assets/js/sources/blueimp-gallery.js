/*
 * blueimp Gallery JS
 * https://github.com/blueimp/Gallery
 *
 * Copyright 2013, Sebastian Tschan
 * https://blueimp.net
 *
 * Swipe implementation based on
 * https://github.com/bradbirdsall/Swipe
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global define, DocumentTouch */

/* eslint-disable no-param-reassign */

; (function (factory) {
	'use strict'
	if (typeof define === 'function' && define.amd) {
		// Register as an anonymous AMD module:
		define(['./blueimp-helper'], factory)
	} else {
		// Browser globals:
		window.blueimp = window.blueimp || {}
		window.blueimp.Gallery = factory(window.blueimp.helper || window.jQuery)
	}
})
	(function ($) {
		'use strict'

		/**
		 * Gallery constructor
		 *
		 * @class
		 * @param {Array|NodeList} list Gallery content
		 * @param {object} [options] Gallery options
		 * @returns {object} Gallery object
		 */
		function Gallery(list, options) {
			if (document.body.style.maxHeight === undefined) {
				// document.body.style.maxHeight is undefined on IE6 and lower
				return null
			}
			if (!this || this.options !== Gallery.prototype.options) {
				// Called as function instead of as constructor,
				// so we simply return a new instance:
				return new Gallery(list, options)
			}
			if (!list || !list.length) {
				this.console.log(
					'blueimp Gallery: No or empty list provided as first argument.',
					list
				)
				return
			}
			this.list = list
			this.num = list.length
			this.initOptions(options)
			this.initialize()
		}

		$.extend(Gallery.prototype, {
			options: {
				// The Id, element or querySelector of the gallery widget:
				container: '#blueimp-gallery',
				// The tag name, Id, element or querySelector of the slides container:
				slidesContainer: 'div',
				// The tag name, Id, element or querySelector of the title element:
				titleElement: 'h3',
				// The class to add when the gallery is visible:
				displayClass: 'blueimp-gallery-display',
				// The class to add when the gallery controls are visible:
				controlsClass: 'blueimp-gallery-controls',
				// The class to add when the gallery only displays one element:
				singleClass: 'blueimp-gallery-single',
				// The class to add when the left edge has been reached:
				leftEdgeClass: 'blueimp-gallery-left',
				// The class to add when the right edge has been reached:
				rightEdgeClass: 'blueimp-gallery-right',
				// The class to add when the automatic slideshow is active:
				playingClass: 'blueimp-gallery-playing',
				// The class to add when the browser supports SVG as img (or background):
				svgasimgClass: 'blueimp-gallery-svgasimg',
				// The class to add when the browser supports SMIL (animated SVGs):
				smilClass: 'blueimp-gallery-smil',
				// The class for all slides:
				slideClass: 'slide',
				// The slide class for the active (current index) slide:
				slideActiveClass: 'slide-active',
				// The slide class for the previous (before current index) slide:
				slidePrevClass: 'slide-prev',
				// The slide class for the next (after current index) slide:
				slideNextClass: 'slide-next',
				// The slide class for loading elements:
				slideLoadingClass: 'slide-loading',
				// The slide class for elements that failed to load:
				slideErrorClass: 'slide-error',
				// The class for the content element loaded into each slide:
				slideContentClass: 'slide-content',
				// The class for the "toggle" control:
				toggleClass: 'toggle',
				// The class for the "prev" control:
				prevClass: 'prev',
				// The class for the "next" control:
				nextClass: 'next',
				// The class for the "close" control:
				closeClass: 'close',
				// The class for the "play-pause" toggle control:
				playPauseClass: 'play-pause',
				// The list object property (or data attribute) with the object type:
				typeProperty: 'type',
				// The list object property (or data attribute) with the object title:
				titleProperty: 'title',
				// The list object property (or data attribute) with the object alt text:
				altTextProperty: 'alt',
				// The list object property (or data attribute) with the object URL:
				urlProperty: 'href',
				// The list object property (or data attribute) with the object srcset:
				srcsetProperty: 'srcset',
				// The list object property (or data attribute) with the object sizes:
				sizesProperty: 'sizes',
				// The list object property (or data attribute) with the object sources:
				sourcesProperty: 'sources',
				// The gallery listens for transitionend events before triggering the
				// opened and closed events, unless the following option is set to false:
				displayTransition: true,
				// Defines if the gallery slides are cleared from the gallery modal,
				// or reused for the next gallery initialization:
				clearSlides: true,
				// Toggle the controls on pressing the Enter key:
				toggleControlsOnEnter: true,
				// Toggle the controls on slide click:
				toggleControlsOnSlideClick: true,
				// Toggle the automatic slideshow interval on pressing the Space key:
				toggleSlideshowOnSpace: true,
				// Navigate the gallery by pressing the ArrowLeft and ArrowRight keys:
				enableKeyboardNavigation: true,
				// Close the gallery on pressing the Escape key:
				closeOnEscape: true,
				// Close the gallery when clicking on an empty slide area:
				closeOnSlideClick: true,
				// Close the gallery by swiping up or down:
				closeOnSwipeUpOrDown: true,
				// Close the gallery when the URL hash changes:
				closeOnHashChange: true,
				// Emulate touch events on mouse-pointer devices such as desktop browsers:
				emulateTouchEvents: true,
				// Stop touch events from bubbling up to ancestor elements of the Gallery:
				stopTouchEventsPropagation: false,
				// Hide the page scrollbars:
				hidePageScrollbars: true,
				// Stops any touches on the container from scrolling the page:
				disableScroll: true,
				// Carousel mode (shortcut for carousel specific options):
				carousel: false,
				// Allow continuous navigation, moving from last to first
				// and from first to last slide:
				continuous: true,
				// Remove elements outside of the preload range from the DOM:
				unloadElements: true,
				// Start with the automatic slideshow:
				startSlideshow: false,
				// Delay in milliseconds between slides for the automatic slideshow:
				slideshowInterval: 5000,
				// The direction the slides are moving: ltr=LeftToRight or rtl=RightToLeft
				slideshowDirection: 'ltr',
				// The starting index as integer.
				// Can also be an object of the given list,
				// or an equal object with the same url property:
				index: 0,
				// The number of elements to load around the current index:
				preloadRange: 2,
				// The transition duration between slide changes in milliseconds:
				transitionDuration: 300,
				// The transition duration for automatic slide changes, set to an integer
				// greater 0 to override the default transition duration:
				slideshowTransitionDuration: 500,
				// The event object for which the default action will be canceled
				// on Gallery initialization (e.g. the click event to open the Gallery):
				event: undefined,
				// Callback function executed when the Gallery is initialized.
				// Is called with the gallery instance as "this" object:
				onopen: undefined,
				// Callback function executed when the Gallery has been initialized
				// and the initialization transition has been completed.
				// Is called with the gallery instance as "this" object:
				onopened: undefined,
				// Callback function executed on slide change.
				// Is called with the gallery instance as "this" object and the
				// current index and slide as arguments:
				onslide: undefined,
				// Callback function executed after the slide change transition.
				// Is called with the gallery instance as "this" object and the
				// current index and slide as arguments:
				onslideend: undefined,
				// Callback function executed on slide content load.
				// Is called with the gallery instance as "this" object and the
				// slide index and slide element as arguments:
				onslidecomplete: undefined,
				// Callback function executed when the Gallery is about to be closed.
				// Is called with the gallery instance as "this" object:
				onclose: undefined,
				// Callback function executed when the Gallery has been closed
				// and the closing transition has been completed.
				// Is called with the gallery instance as "this" object:
				onclosed: undefined
			},

			carouselOptions: {
				hidePageScrollbars: false,
				toggleControlsOnEnter: false,
				toggleSlideshowOnSpace: false,
				enableKeyboardNavigation: false,
				closeOnEscape: false,
				closeOnSlideClick: false,
				closeOnSwipeUpOrDown: false,
				closeOnHashChange: false,
				disableScroll: false,
				startSlideshow: true
			},

			console:
				window.console && typeof window.console.log === 'function'
					? window.console
					: { log: function () { } },

			// Detect touch, transition, transform and background-size support:
			support: (function (element) {
				var support = {
					source: !!window.HTMLSourceElement,
					picture: !!window.HTMLPictureElement,
					svgasimg: document.implementation.hasFeature(
						'http://www.w3.org/TR/SVG11/feature#Image',
						'1.1'
					),
					smil:
						!!document.createElementNS &&
						/SVGAnimate/.test(
							document
								.createElementNS('http://www.w3.org/2000/svg', 'animate')
								.toString()
						),
					touch:
						window.ontouchstart !== undefined ||
						(window.DocumentTouch && document instanceof DocumentTouch)
				}
				var transitions = {
					webkitTransition: {
						end: 'webkitTransitionEnd',
						prefix: '-webkit-'
					},
					MozTransition: {
						end: 'transitionend',
						prefix: '-moz-'
					},
					OTransition: {
						end: 'otransitionend',
						prefix: '-o-'
					},
					transition: {
						end: 'transitionend',
						prefix: ''
					}
				}
				var prop
				for (prop in transitions) {
					if (
						Object.prototype.hasOwnProperty.call(transitions, prop) &&
						element.style[prop] !== undefined
					) {
						support.transition = transitions[prop]
						support.transition.name = prop
						break
					}
				}
				/**
				 * Tests browser support
				 */
				function elementTests() {
					var transition = support.transition
					var prop
					var translateZ
					document.body.appendChild(element)
					if (transition) {
						prop = transition.name.slice(0, -9) + 'ransform'
						if (element.style[prop] !== undefined) {
							element.style[prop] = 'translateZ(0)'
							translateZ = window
								.getComputedStyle(element)
								.getPropertyValue(transition.prefix + 'transform')
							support.transform = {
								prefix: transition.prefix,
								name: prop,
								translate: true,
								translateZ: !!translateZ && translateZ !== 'none'
							}
						}
					}
					document.body.removeChild(element)
				}
				if (document.body) {
					elementTests()
				} else {
					$(document).on('DOMContentLoaded', elementTests)
				}
				return support
				// Test element, has to be standard HTML and must not be hidden
				// for the CSS3 tests using window.getComputedStyle to be applicable:
			})(document.createElement('div')),

			requestAnimationFrame:
				window.requestAnimationFrame ||
				window.webkitRequestAnimationFrame ||
				window.mozRequestAnimationFrame,

			cancelAnimationFrame:
				window.cancelAnimationFrame ||
				window.webkitCancelRequestAnimationFrame ||
				window.webkitCancelAnimationFrame ||
				window.mozCancelAnimationFrame,

			initialize: function () {
				this.initStartIndex()
				if (this.initWidget() === false) {
					return false
				}
				this.initEventListeners()
				// Load the slide at the given index:
				this.onslide(this.index)
				// Manually trigger the slideend event for the initial slide:
				this.ontransitionend()
				// Start the automatic slideshow if applicable:
				if (this.options.startSlideshow) {
					this.play()
				}
			},

			slide: function (to, duration) {
				window.clearTimeout(this.timeout)
				var index = this.index
				var direction
				var naturalDirection
				var diff
				if (index === to || this.num === 1) {
					return
				}
				if (!duration) {
					duration = this.options.transitionDuration
				}
				if (this.support.transform) {
					if (!this.options.continuous) {
						to = this.circle(to)
					}
					// 1: backward, -1: forward:
					direction = Math.abs(index - to) / (index - to)
					// Get the actual position of the slide:
					if (this.options.continuous) {
						naturalDirection = direction
						direction = -this.positions[this.circle(to)] / this.slideWidth
						// If going forward but to < index, use to = slides.length + to
						// If going backward but to > index, use to = -slides.length + to
						if (direction !== naturalDirection) {
							to = -direction * this.num + to
						}
					}
					diff = Math.abs(index - to) - 1
					// Move all the slides between index and to in the right direction:
					while (diff) {
						diff -= 1
						this.move(
							this.circle((to > index ? to : index) - diff - 1),
							this.slideWidth * direction,
							0
						)
					}
					to = this.circle(to)
					this.move(index, this.slideWidth * direction, duration)
					this.move(to, 0, duration)
					if (this.options.continuous) {
						this.move(
							this.circle(to - direction),
							-(this.slideWidth * direction),
							0
						)
					}
				} else {
					to = this.circle(to)
					this.animate(index * -this.slideWidth, to * -this.slideWidth, duration)
				}
				this.onslide(to)
			},

			getIndex: function () {
				return this.index
			},

			getNumber: function () {
				return this.num
			},

			prev: function () {
				if (this.options.continuous || this.index) {
					this.slide(this.index - 1)
				}
			},

			next: function () {
				if (this.options.continuous || this.index < this.num - 1) {
					this.slide(this.index + 1)
				}
			},

			play: function (time) {
				var that = this
				var nextIndex =
					this.index + (this.options.slideshowDirection === 'rtl' ? -1 : 1)
				window.clearTimeout(this.timeout)
				this.interval = time || this.options.slideshowInterval
				if (this.elements[this.index] > 1) {
					this.timeout = this.setTimeout(
						(!this.requestAnimationFrame && this.slide) ||
						function (to, duration) {
							that.animationFrameId = that.requestAnimationFrame.call(
								window,
								function () {
									that.slide(to, duration)
								}
							)
						},
						[nextIndex, this.options.slideshowTransitionDuration],
						this.interval
					)
				}
				this.container.addClass(this.options.playingClass)
				this.slidesContainer[0].setAttribute('aria-live', 'off')
				if (this.playPauseElement.length) {
					this.playPauseElement[0].setAttribute('aria-pressed', 'true')
				}
			},

			pause: function () {
				window.clearTimeout(this.timeout)
				this.interval = null
				if (this.cancelAnimationFrame) {
					this.cancelAnimationFrame.call(window, this.animationFrameId)
					this.animationFrameId = null
				}
				this.container.removeClass(this.options.playingClass)
				this.slidesContainer[0].setAttribute('aria-live', 'polite')
				if (this.playPauseElement.length) {
					this.playPauseElement[0].setAttribute('aria-pressed', 'false')
				}
			},

			add: function (list) {
				var i
				if (!list.concat) {
					// Make a real array out of the list to add:
					list = Array.prototype.slice.call(list)
				}

				this.num = this.list.length
				if (!this.list.concat) {
					// Make a real array out of the Gallery list:
					this.list = Array.prototype.slice.call(this.list)
				}
				this.list = this.list.concat(list)
				this.num = this.list.length
				if (this.num > 2 && this.options.continuous === null) {
					this.options.continuous = true
					this.container.removeClass(this.options.leftEdgeClass)
				}
				this.container
					.removeClass(this.options.rightEdgeClass)
					.removeClass(this.options.singleClass)
				for (i = this.num - list.length; i < this.num; i += 1) {
					this.addSlide(i)
					this.positionSlide(i)
				}
				this.positions.length = this.num
				this.initSlides(true)
			},

			resetSlides: function () {
				this.slidesContainer.empty()
				this.unloadAllSlides()
				this.slides = []
			},

			handleClose: function () {
				var options = this.options
				this.destroyEventListeners()
				// Cancel the slideshow:
				this.pause()
				this.container[0].style.display = 'none'
				this.container
					.removeClass(options.displayClass)
					.removeClass(options.singleClass)
					.removeClass(options.leftEdgeClass)
					.removeClass(options.rightEdgeClass)
				if (options.hidePageScrollbars) {
					document.body.style.overflow = this.bodyOverflowStyle
				}
				if (this.options.clearSlides) {
					this.resetSlides()
				}
				if (this.options.onclosed) {
					this.options.onclosed.call(this)
				}
			},

			close: function () {
				var that = this
				/**
				 * Close handler
				 *
				 * @param {event} event Close event
				 */
				function closeHandler(event) {
					if (event.target === that.container[0]) {
						that.container.off(that.support.transition.end, closeHandler)
						that.handleClose()
					}
				}
				if (this.options.onclose) {
					this.options.onclose.call(this)
				}
				if (this.support.transition && this.options.displayTransition) {
					this.container.on(this.support.transition.end, closeHandler)
					this.container.removeClass(this.options.displayClass)
				} else {
					this.handleClose()
				}
			},

			circle: function (index) {
				// Always return a number inside of the slides index range:
				return (this.num + (index % this.num)) % this.num
			},

			move: function (index, dist, duration) {
				this.translateX(index, dist, duration)
				this.positions[index] = dist
			},

			translate: function (index, x, y, duration) {
				if (!this.slides[index]) return
				var style = this.slides[index].style
				var transition = this.support.transition
				var transform = this.support.transform
				style[transition.name + 'Duration'] = duration + 'ms'
				style[transform.name] =
					'translate(' +
					x +
					'px, ' +
					y +
					'px)' +
					(transform.translateZ ? ' translateZ(0)' : '')
			},

			translateX: function (index, x, duration) {
				this.translate(index, x, 0, duration)
			},

			translateY: function (index, y, duration) {
				this.translate(index, 0, y, duration)
			},

			animate: function (from, to, duration) {
				if (!duration) {
					this.slidesContainer[0].style.left = to + 'px'
					return
				}
				var that = this
				var start = new Date().getTime()
				var timer = window.setInterval(function () {
					var timeElap = new Date().getTime() - start
					if (timeElap > duration) {
						that.slidesContainer[0].style.left = to + 'px'
						that.ontransitionend()
						window.clearInterval(timer)
						return
					}
					that.slidesContainer[0].style.left =
						(to - from) * (Math.floor((timeElap / duration) * 100) / 100) +
						from +
						'px'
				}, 4)
			},

			preventDefault: function (event) {
				if (event.preventDefault) {
					event.preventDefault()
				} else {
					event.returnValue = false
				}
			},

			stopPropagation: function (event) {
				if (event.stopPropagation) {
					event.stopPropagation()
				} else {
					event.cancelBubble = true
				}
			},

			onresize: function () {
				this.initSlides(true)
			},

			onhashchange: function () {
				if (this.options.closeOnHashChange) {
					this.close()
				}
			},

			onmousedown: function (event) {
				// Trigger on clicks of the left mouse button only
				// and exclude video & audio elements:
				if (
					event.which &&
					event.which === 1 &&
					event.target.nodeName !== 'VIDEO' &&
					event.target.nodeName !== 'AUDIO'
				) {
					// Preventing the default mousedown action is required
					// to make touch emulation work with Firefox:
					event.preventDefault()
						; (event.originalEvent || event).touches = [
							{
								pageX: event.pageX,
								pageY: event.pageY
							}
						]
					this.ontouchstart(event)
				}
			},

			onmousemove: function (event) {
				if (this.touchStart) {
					; (event.originalEvent || event).touches = [
						{
							pageX: event.pageX,
							pageY: event.pageY
						}
					]
					this.ontouchmove(event)
				}
			},

			onmouseup: function (event) {
				if (this.touchStart) {
					this.ontouchend(event)
					delete this.touchStart
				}
			},

			onmouseout: function (event) {
				if (this.touchStart) {
					var target = event.target
					var related = event.relatedTarget
					if (!related || (related !== target && !$.contains(target, related))) {
						this.onmouseup(event)
					}
				}
			},

			ontouchstart: function (event) {
				if (this.options.stopTouchEventsPropagation) {
					this.stopPropagation(event)
				}
				// jQuery doesn't copy touch event properties by default,
				// so we have to access the originalEvent object:
				var touch = (event.originalEvent || event).touches[0]
				this.touchStart = {
					// Remember the initial touch coordinates:
					x: touch.pageX,
					y: touch.pageY,
					// Store the time to determine touch duration:
					time: Date.now()
				}
				// Helper variable to detect scroll movement:
				this.isScrolling = undefined
				// Reset delta values:
				this.touchDelta = {}
			},

			ontouchmove: function (event) {
				if (this.options.stopTouchEventsPropagation) {
					this.stopPropagation(event)
				}
				// jQuery doesn't copy touch event properties by default,
				// so we have to access the originalEvent object:
				var touches = (event.originalEvent || event).touches
				var touch = touches[0]
				var scale = (event.originalEvent || event).scale
				var index = this.index
				var touchDeltaX
				var indices
				// Ensure this is a one touch swipe and not, e.g. a pinch:
				if (touches.length > 1 || (scale && scale !== 1)) {
					return
				}
				if (this.options.disableScroll) {
					event.preventDefault()
				}
				// Measure change in x and y coordinates:
				this.touchDelta = {
					x: touch.pageX - this.touchStart.x,
					y: touch.pageY - this.touchStart.y
				}
				touchDeltaX = this.touchDelta.x
				// Detect if this is a vertical scroll movement (run only once per touch):
				if (this.isScrolling === undefined) {
					this.isScrolling =
						this.isScrolling ||
						Math.abs(touchDeltaX) < Math.abs(this.touchDelta.y)
				}
				if (!this.isScrolling) {
					// Always prevent horizontal scroll:
					event.preventDefault()
					// Stop the slideshow:
					window.clearTimeout(this.timeout)
					if (this.options.continuous) {
						indices = [this.circle(index + 1), index, this.circle(index - 1)]
					} else {
						// Increase resistance if first slide and sliding left
						// or last slide and sliding right:
						this.touchDelta.x = touchDeltaX =
							touchDeltaX /
							((!index && touchDeltaX > 0) ||
								(index === this.num - 1 && touchDeltaX < 0)
								? Math.abs(touchDeltaX) / this.slideWidth + 1
								: 1)
						indices = [index]
						if (index) {
							indices.push(index - 1)
						}
						if (index < this.num - 1) {
							indices.unshift(index + 1)
						}
					}
					while (indices.length) {
						index = indices.pop()
						this.translateX(index, touchDeltaX + this.positions[index], 0)
					}
				} else if (!this.options.carousel) {
					this.translateY(index, this.touchDelta.y + this.positions[index], 0)
				}
			},

			ontouchend: function (event) {
				if (this.options.stopTouchEventsPropagation) {
					this.stopPropagation(event)
				}
				var index = this.index
				var absTouchDeltaX = Math.abs(this.touchDelta.x)
				var slideWidth = this.slideWidth
				var duration = Math.ceil(
					(this.options.transitionDuration * (1 - absTouchDeltaX / slideWidth)) /
					2
				)
				// Determine if slide attempt triggers next/prev slide:
				var isValidSlide = absTouchDeltaX > 20
				// Determine if slide attempt is past start or end:
				var isPastBounds =
					(!index && this.touchDelta.x > 0) ||
					(index === this.num - 1 && this.touchDelta.x < 0)
				var isValidClose =
					!isValidSlide &&
					this.options.closeOnSwipeUpOrDown &&
					Math.abs(this.touchDelta.y) > 20
				var direction
				var indexForward
				var indexBackward
				var distanceForward
				var distanceBackward
				if (this.options.continuous) {
					isPastBounds = false
				}
				// Determine direction of swipe (true: right, false: left):
				direction = this.touchDelta.x < 0 ? -1 : 1
				if (!this.isScrolling) {
					if (isValidSlide && !isPastBounds) {
						indexForward = index + direction
						indexBackward = index - direction
						distanceForward = slideWidth * direction
						distanceBackward = -slideWidth * direction
						if (this.options.continuous) {
							this.move(this.circle(indexForward), distanceForward, 0)
							this.move(this.circle(index - 2 * direction), distanceBackward, 0)
						} else if (indexForward >= 0 && indexForward < this.num) {
							this.move(indexForward, distanceForward, 0)
						}
						this.move(index, this.positions[index] + distanceForward, duration)
						this.move(
							this.circle(indexBackward),
							this.positions[this.circle(indexBackward)] + distanceForward,
							duration
						)
						index = this.circle(indexBackward)
						this.onslide(index)
					} else {
						// Move back into position
						if (this.options.continuous) {
							this.move(this.circle(index - 1), -slideWidth, duration)
							this.move(index, 0, duration)
							this.move(this.circle(index + 1), slideWidth, duration)
						} else {
							if (index) {
								this.move(index - 1, -slideWidth, duration)
							}
							this.move(index, 0, duration)
							if (index < this.num - 1) {
								this.move(index + 1, slideWidth, duration)
							}
						}
					}
				} else {
					if (isValidClose) {
						this.close()
					} else {
						// Move back into position
						this.translateY(index, 0, duration)
					}
				}
			},

			ontouchcancel: function (event) {
				if (this.touchStart) {
					this.ontouchend(event)
					delete this.touchStart
				}
			},

			ontransitionend: function (event) {
				var slide = this.slides[this.index]
				if (!event || slide === event.target) {
					if (this.interval) {
						this.play()
					}
					this.setTimeout(this.options.onslideend, [this.index, slide])
				}
			},

			oncomplete: function (event) {
				var target = event.target || event.srcElement
				var parent = target && target.parentNode
				var index
				if (!target || !parent) {
					return
				}
				index = this.getNodeIndex(parent)
				$(parent).removeClass(this.options.slideLoadingClass)
				if (event.type === 'error') {
					$(parent).addClass(this.options.slideErrorClass)
					this.elements[index] = 3 // Fail
				} else {
					this.elements[index] = 2 // Done
				}
				// Fix for IE7's lack of support for percentage max-height:
				if (target.clientHeight > this.container[0].clientHeight) {
					target.style.maxHeight = this.container[0].clientHeight
				}
				if (this.interval && this.slides[this.index] === parent) {
					this.play()
				}
				this.setTimeout(this.options.onslidecomplete, [index, parent])
			},

			onload: function (event) {
				this.oncomplete(event)
			},

			onerror: function (event) {
				this.oncomplete(event)
			},

			onkeydown: function (event) {
				switch (event.which || event.keyCode) {
					case 13: // Enter
						if (this.options.toggleControlsOnEnter) {
							this.preventDefault(event)
							this.toggleControls()
						}
						break
					case 27: // Escape
						if (this.options.closeOnEscape) {
							this.close()
							// prevent Escape from closing other things
							event.stopImmediatePropagation()
						}
						break
					case 32: // Space
						if (this.options.toggleSlideshowOnSpace) {
							this.preventDefault(event)
							this.toggleSlideshow()
						}
						break
					case 37: // ArrowLeft
						if (this.options.enableKeyboardNavigation) {
							this.preventDefault(event)
							this.prev()
						}
						break
					case 39: // ArrowRight
						if (this.options.enableKeyboardNavigation) {
							this.preventDefault(event)
							this.next()
						}
						break
				}
			},

			handleClick: function (event) {
				var options = this.options
				var target = event.target || event.srcElement
				var parent = target.parentNode
				/**
				 * Checks if the target from the close has the given class
				 *
				 * @param {string} className Class name
				 * @returns {boolean} Returns true if the target has the class name
				 */
				function isTarget(className) {
					return $(target).hasClass(className) || $(parent).hasClass(className)
				}
				if (isTarget(options.toggleClass)) {
					// Click on "toggle" control
					this.preventDefault(event)
					this.toggleControls()
				} else if (isTarget(options.prevClass)) {
					// Click on "prev" control
					this.preventDefault(event)
					this.prev()
				} else if (isTarget(options.nextClass)) {
					// Click on "next" control
					this.preventDefault(event)
					this.next()
				} else if (isTarget(options.closeClass)) {
					// Click on "close" control
					this.preventDefault(event)
					this.close()
				} else if (isTarget(options.playPauseClass)) {
					// Click on "play-pause" control
					this.preventDefault(event)
					this.toggleSlideshow()
				} else if (parent === this.slidesContainer[0]) {
					// Click on slide background
					if (options.closeOnSlideClick) {
						this.preventDefault(event)
						this.close()
					} else if (options.toggleControlsOnSlideClick) {
						this.preventDefault(event)
						this.toggleControls()
					}
				} else if (
					parent.parentNode &&
					parent.parentNode === this.slidesContainer[0]
				) {
					// Click on displayed element
					if (options.toggleControlsOnSlideClick) {
						this.preventDefault(event)
						this.toggleControls()
					}
				}
			},

			onclick: function (event) {
				if (
					this.options.emulateTouchEvents &&
					this.touchDelta &&
					(Math.abs(this.touchDelta.x) > 20 || Math.abs(this.touchDelta.y) > 20)
				) {
					delete this.touchDelta
					return
				}
				return this.handleClick(event)
			},

			updateEdgeClasses: function (index) {
				if (!index) {
					this.container.addClass(this.options.leftEdgeClass)
				} else {
					this.container.removeClass(this.options.leftEdgeClass)
				}
				if (index === this.num - 1) {
					this.container.addClass(this.options.rightEdgeClass)
				} else {
					this.container.removeClass(this.options.rightEdgeClass)
				}
			},

			updateActiveSlide: function (oldIndex, newIndex) {
				var slides = this.slides
				var options = this.options
				var list = [
					{
						index: newIndex,
						method: 'addClass',
						hidden: false
					},
					{
						index: oldIndex,
						method: 'removeClass',
						hidden: true
					}
				]
				var item, index
				while (list.length) {
					item = list.pop()
					$(slides[item.index])[item.method](options.slideActiveClass)
					index = this.circle(item.index - 1)
					if (options.continuous || index < item.index) {
						$(slides[index])[item.method](options.slidePrevClass)
					}
					index = this.circle(item.index + 1)
					if (options.continuous || index > item.index) {
						$(slides[index])[item.method](options.slideNextClass)
					}
				}
				this.slides[oldIndex].setAttribute('aria-hidden', 'true')
				this.slides[newIndex].removeAttribute('aria-hidden')
			},

			handleSlide: function (oldIndex, newIndex) {
				if (!this.options.continuous) {
					this.updateEdgeClasses(newIndex)
				}
				this.updateActiveSlide(oldIndex, newIndex)
				this.loadElements(newIndex)
				if (this.options.unloadElements) {
					this.unloadElements(oldIndex, newIndex)
				}
				this.setTitle(newIndex)
			},

			onslide: function (index) {
				this.handleSlide(this.index, index)
				this.index = index
				this.setTimeout(this.options.onslide, [index, this.slides[index]])
			},

			setTitle: function (index) {
				var firstChild = this.slides[index].firstChild
				var text = firstChild.title || firstChild.alt
				var titleElement = this.titleElement
				if (titleElement.length) {
					this.titleElement.empty()
					if (text) {
						titleElement[0].appendChild(document.createTextNode(text))
					}
				}
			},

			setTimeout: function (func, args, wait) {
				var that = this
				return (
					func &&
					window.setTimeout(function () {
						func.apply(that, args || [])
					}, wait || 0)
				)
			},

			imageFactory: function (obj, callback) {
				var options = this.options
				var that = this
				var url = obj
				var img = this.imagePrototype.cloneNode(false)
				var picture
				var called
				var sources
				var srcset
				var sizes
				var title
				var altText
				var i
				/**
				 * Wraps the callback function for the load/error event
				 *
				 * @param {event} event load/error event
				 * @returns {number} timeout ID
				 */
				function callbackWrapper(event) {
					if (!called) {
						event = {
							type: event.type,
							target: picture || img
						}
						if (!event.target.parentNode) {
							// Fix for browsers (e.g. IE7) firing the load event for
							// cached images before the element could
							// be added to the DOM:
							return that.setTimeout(callbackWrapper, [event])
						}
						called = true
						$(img).off('load error', callbackWrapper)
						callback(event)
					}
				}
				if (typeof url !== 'string') {
					url = this.getItemProperty(obj, options.urlProperty)
					sources =
						this.support.picture &&
						this.support.source &&
						this.getItemProperty(obj, options.sourcesProperty)
					srcset = this.getItemProperty(obj, options.srcsetProperty)
					sizes = this.getItemProperty(obj, options.sizesProperty)
					title = this.getItemProperty(obj, options.titleProperty)
					altText = this.getItemProperty(obj, options.altTextProperty) || title
				}
				img.draggable = false
				if (title) {
					img.title = title
				}
				if (altText) {
					img.alt = altText
				}
				$(img).on('load error', callbackWrapper)
				if (sources && sources.length) {
					picture = this.picturePrototype.cloneNode(false)
					for (i = 0; i < sources.length; i += 1) {
						picture.appendChild(
							$.extend(this.sourcePrototype.cloneNode(false), sources[i])
						)
					}
					picture.appendChild(img)
					$(picture).addClass(options.toggleClass)
				}
				if (srcset) {
					if (sizes) {
						img.sizes = sizes
					}
					img.srcset = srcset
				}
				img.src = url
				if (picture) return picture
				return img
			},

			createElement: function (obj, callback) {
				var type = obj && this.getItemProperty(obj, this.options.typeProperty)
				var factory =
					(type && this[type.split('/')[0] + 'Factory']) || this.imageFactory
				var element = obj && factory.call(this, obj, callback)
				if (!element) {
					element = this.elementPrototype.cloneNode(false)
					this.setTimeout(callback, [
						{
							type: 'error',
							target: element
						}
					])
				}
				$(element).addClass(this.options.slideContentClass)
				return element
			},

			iteratePreloadRange: function (index, func) {
				var num = this.num
				var options = this.options
				var limit = Math.min(num, options.preloadRange * 2 + 1)
				var j = index
				var i
				for (i = 0; i < limit; i += 1) {
					// First iterate to the current index (0),
					// then the next one (+1),
					// then the previous one (-1),
					// then the next after next (+2),
					// then the one before the previous one (-2), etc.:
					j += i * (i % 2 === 0 ? -1 : 1)
					if (j < 0 || j >= num) {
						if (!options.continuous) continue
						// Connect the ends of the list to load slide elements for
						// continuous iteration:
						j = this.circle(j)
					}
					func.call(this, j)
				}
			},

			loadElement: function (index) {
				if (!this.elements[index]) {
					if (this.slides[index].firstChild) {
						this.elements[index] = $(this.slides[index]).hasClass(
							this.options.slideErrorClass
						)
							? 3
							: 2
					} else {
						this.elements[index] = 1 // Loading
						$(this.slides[index]).addClass(this.options.slideLoadingClass)
						this.slides[index].appendChild(
							this.createElement(this.list[index], this.proxyListener)
						)
					}
				}
			},

			loadElements: function (index) {
				this.iteratePreloadRange(index, this.loadElement)
			},

			unloadElements: function (oldIndex, newIndex) {
				var preloadRange = this.options.preloadRange
				this.iteratePreloadRange(oldIndex, function (i) {
					var diff = Math.abs(i - newIndex)
					if (diff > preloadRange && diff + preloadRange < this.num) {
						this.unloadSlide(i)
						delete this.elements[i]
					}
				})
			},

			addSlide: function (index) {
				var slide = this.slidePrototype.cloneNode(false)
				slide.setAttribute('data-index', index)
				slide.setAttribute('aria-hidden', 'true')
				this.slidesContainer[0].appendChild(slide)
				this.slides.push(slide)
			},

			positionSlide: function (index) {
				var slide = this.slides[index]
				slide.style.width = this.slideWidth + 'px'
				if (this.support.transform) {
					slide.style.left = index * -this.slideWidth + 'px'
					this.move(
						index,
						this.index > index
							? -this.slideWidth
							: this.index < index
								? this.slideWidth
								: 0,
						0
					)
				}
			},

			initSlides: function (reload) {
				var clearSlides, i
				if (!reload) {
					this.positions = []
					this.positions.length = this.num
					this.elements = {}
					this.picturePrototype =
						this.support.picture && document.createElement('picture')
					this.sourcePrototype =
						this.support.source && document.createElement('source')
					this.imagePrototype = document.createElement('img')
					this.elementPrototype = document.createElement('div')
					this.slidePrototype = this.elementPrototype.cloneNode(false)
					$(this.slidePrototype).addClass(this.options.slideClass)
					this.slides = this.slidesContainer[0].children
					clearSlides =
						this.options.clearSlides || this.slides.length !== this.num
				}
				this.slideWidth = this.container[0].clientWidth
				this.slideHeight = this.container[0].clientHeight
				this.slidesContainer[0].style.width = this.num * this.slideWidth + 'px'
				if (clearSlides) {
					this.resetSlides()
				}

				var found = []
				var uniqueList = []
				for (i = 0; i < this.num; i += 1) {
					var el = this.list[i],
						prop = this.getItemProperty(el, this.options.urlProperty)
					if (found.indexOf(prop) === -1) {
						uniqueList.push(el)
						found.push(prop)
					}
				};
				this.list = uniqueList
				this.num = this.list.length

				for (i = 0; i < this.num; i += 1) {
					if (clearSlides) {
						this.addSlide(i)
					}
					this.positionSlide(i)
				}
				// Reposition the slides before and after the given index:
				if (this.options.continuous && this.support.transform) {
					this.move(this.circle(this.index - 1), -this.slideWidth, 0)
					this.move(this.circle(this.index + 1), this.slideWidth, 0)
				}
				if (!this.support.transform) {
					this.slidesContainer[0].style.left =
						this.index * -this.slideWidth + 'px'
				}
			},

			unloadSlide: function (index) {
				var slide, firstChild
				slide = this.slides[index]
				firstChild = slide.firstChild
				if (firstChild !== null) {
					slide.removeChild(firstChild)
				}
			},

			unloadAllSlides: function () {
				var i, len
				for (i = 0, len = this.slides.length; i < len; i++) {
					this.unloadSlide(i)
				}
			},

			toggleControls: function () {
				var controlsClass = this.options.controlsClass
				if (this.container.hasClass(controlsClass)) {
					this.container.removeClass(controlsClass)
				} else {
					this.container.addClass(controlsClass)
				}
			},

			toggleSlideshow: function () {
				if (!this.interval) {
					this.play()
				} else {
					this.pause()
				}
			},

			getNodeIndex: function (element) {
				return parseInt(element.getAttribute('data-index'), 10)
			},

			getNestedProperty: function (obj, property) {
				property.replace(
					// Matches native JavaScript notation in a String,
					// e.g. '["doubleQuoteProp"].dotProp[2]'
					// eslint-disable-next-line no-useless-escape
					/\[(?:'([^']+)'|"([^"]+)"|(\d+))\]|(?:(?:^|\.)([^\.\[]+))/g,
					function (str, singleQuoteProp, doubleQuoteProp, arrayIndex, dotProp) {
						var prop =
							dotProp ||
							singleQuoteProp ||
							doubleQuoteProp ||
							(arrayIndex && parseInt(arrayIndex, 10))
						if (str && obj) {
							obj = obj[prop]
						}
					}
				)
				return obj
			},

			getDataProperty: function (obj, property) {
				var key
				var prop
				if (obj.dataset) {
					key = property.replace(/-([a-z])/g, function (_, b) {
						return b.toUpperCase()
					})
					prop = obj.dataset[key]
				} else if (obj.getAttribute) {
					prop = obj.getAttribute(
						'data-' + property.replace(/([A-Z])/g, '-$1').toLowerCase()
					)
				}
				if (typeof prop === 'string') {
					// eslint-disable-next-line no-useless-escape
					if (
						/^(true|false|null|-?\d+(\.\d+)?|\{[\s\S]*\}|\[[\s\S]*\])$/.test(prop)
					) {
						try {
							return $.parseJSON(prop)
						} catch (ignore) {
							// ignore JSON parsing errors
						}
					}
					return prop
				}
			},

			getItemProperty: function (obj, property) {
				var prop = this.getDataProperty(obj, property)
				if (prop === undefined) {
					prop = obj[property]
				}
				if (prop === undefined) {
					prop = this.getNestedProperty(obj, property)
				}
				return prop
			},

			initStartIndex: function () {
				var index = this.options.index
				var urlProperty = this.options.urlProperty
				var i
				// Check if the index is given as a list object:
				if (index && typeof index !== 'number') {
					for (i = 0; i < this.num; i += 1) {
						if (
							this.list[i] === index ||
							this.getItemProperty(this.list[i], urlProperty) ===
							this.getItemProperty(index, urlProperty)
						) {
							index = i
							break
						}
					}
				}
				// Make sure the index is in the list range:
				this.index = this.circle(parseInt(index, 10) || 0)
			},

			initEventListeners: function () {
				var that = this
				var slidesContainer = this.slidesContainer
				/**
				 * Proxy listener
				 *
				 * @param {event} event original event
				 */
				function proxyListener(event) {
					var type =
						that.support.transition && that.support.transition.end === event.type
							? 'transitionend'
							: event.type
					that['on' + type](event)
				}
				$(window).on('resize', proxyListener)
				$(window).on('hashchange', proxyListener)
				$(document.body).on('keydown', proxyListener)
				this.container.on('click', proxyListener)
				if (this.support.touch) {
					slidesContainer.on(
						'touchstart touchmove touchend touchcancel',
						proxyListener
					)
				} else if (this.options.emulateTouchEvents && this.support.transition) {
					slidesContainer.on(
						'mousedown mousemove mouseup mouseout',
						proxyListener
					)
				}
				if (this.support.transition) {
					slidesContainer.on(this.support.transition.end, proxyListener)
				}
				this.proxyListener = proxyListener
			},

			destroyEventListeners: function () {
				var slidesContainer = this.slidesContainer
				var proxyListener = this.proxyListener
				$(window).off('resize', proxyListener)
				$(document.body).off('keydown', proxyListener)
				this.container.off('click', proxyListener)
				if (this.support.touch) {
					slidesContainer.off(
						'touchstart touchmove touchend touchcancel',
						proxyListener
					)
				} else if (this.options.emulateTouchEvents && this.support.transition) {
					slidesContainer.off(
						'mousedown mousemove mouseup mouseout',
						proxyListener
					)
				}
				if (this.support.transition) {
					slidesContainer.off(this.support.transition.end, proxyListener)
				}
			},

			handleOpen: function () {
				if (this.options.onopened) {
					this.options.onopened.call(this)
				}
			},

			initWidget: function () {
				var that = this
				/**
				 * Open handler
				 *
				 * @param {event} event Gallery open event
				 */
				function openHandler(event) {
					if (event.target === that.container[0]) {
						that.container.off(that.support.transition.end, openHandler)
						that.handleOpen()
					}
				}
				this.container = $(this.options.container)
				if (!this.container.length) {
					this.console.log(
						'blueimp Gallery: Widget container not found.',
						this.options.container
					)
					return false
				}
				this.slidesContainer = this.container
					.find(this.options.slidesContainer)
					.first()
				if (!this.slidesContainer.length) {
					this.console.log(
						'blueimp Gallery: Slides container not found.',
						this.options.slidesContainer
					)
					return false
				}
				this.titleElement = this.container.find(this.options.titleElement).first()
				this.playPauseElement = this.container
					.find('.' + this.options.playPauseClass)
					.first()
				if (this.num === 1) {
					this.container.addClass(this.options.singleClass)
				}
				if (this.support.svgasimg) {
					this.container.addClass(this.options.svgasimgClass)
				}
				if (this.support.smil) {
					this.container.addClass(this.options.smilClass)
				}
				if (this.options.onopen) {
					this.options.onopen.call(this)
				}
				if (this.support.transition && this.options.displayTransition) {
					this.container.on(this.support.transition.end, openHandler)
				} else {
					this.handleOpen()
				}
				if (this.options.hidePageScrollbars) {
					// Hide the page scrollbars:
					this.bodyOverflowStyle = document.body.style.overflow
					document.body.style.overflow = 'hidden'
				}
				this.container[0].style.display = 'block'
				this.initSlides()
				this.container.addClass(this.options.displayClass)
			},

			initOptions: function (options) {
				// Create a copy of the prototype options:
				this.options = $.extend({}, this.options)
				// Check if carousel mode is enabled:
				if (
					(options && options.carousel) ||
					(this.options.carousel && (!options || options.carousel !== false))
				) {
					$.extend(this.options, this.carouselOptions)
				}
				// Override any given options:
				$.extend(this.options, options)
				if (this.num < 3) {
					// 1 or 2 slides cannot be displayed continuous,
					// remember the original option by setting to null instead of false:
					this.options.continuous = this.options.continuous ? null : false
				}
				if (!this.support.transition) {
					this.options.emulateTouchEvents = false
				}
				if (this.options.event) {
					this.preventDefault(this.options.event)
				}
			}
		})

		return Gallery
	})

	/*
	 * blueimp Gallery Indicator JS
	 * https://github.com/blueimp/Gallery
	 *
	 * Copyright 2013, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * https://opensource.org/licenses/MIT
	 */

	/* global define */

	; (function (factory) {
		'use strict'
		if (typeof define === 'function' && define.amd) {
			// Register as an anonymous AMD module:
			define(['./blueimp-helper', './blueimp-gallery'], factory)
		} else {
			// Browser globals:
			factory(window.blueimp.helper || window.jQuery, window.blueimp.Gallery)
		}
	})(function ($, Gallery) {
		'use strict'

		var galleryPrototype = Gallery.prototype

		$.extend(galleryPrototype.options, {
			// The tag name, Id, element or querySelector of the indicator container:
			indicatorContainer: 'ol',
			// The class for the active indicator:
			activeIndicatorClass: 'active',
			// The list object property (or data attribute) with the thumbnail URL,
			// used as alternative to a thumbnail child element:
			thumbnailProperty: 'thumbnail',
			// Defines if the gallery indicators should display a thumbnail:
			thumbnailIndicators: true
		})

		var initSlides = galleryPrototype.initSlides
		var addSlide = galleryPrototype.addSlide
		var resetSlides = galleryPrototype.resetSlides
		var handleClick = galleryPrototype.handleClick
		var handleSlide = galleryPrototype.handleSlide
		var handleClose = galleryPrototype.handleClose

		$.extend(galleryPrototype, {
			createIndicator: function (obj) {
				var indicator = this.indicatorPrototype.cloneNode(false)
				var title = this.getItemProperty(obj, this.options.titleProperty)
				var thumbnailProperty = this.options.thumbnailProperty
				var thumbnailUrl
				var thumbnail
				if (this.options.thumbnailIndicators) {
					if (thumbnailProperty) {
						thumbnailUrl = this.getItemProperty(obj, thumbnailProperty)
					}
					if (thumbnailUrl === undefined) {
						thumbnail = obj.getElementsByTagName && $(obj).find('img')[0]
						if (thumbnail) {
							thumbnailUrl = thumbnail.src
							if (!thumbnailUrl && thumbnail.dataset.src !== undefined) {
								thumbnailUrl = thumbnail.dataset.src
							}
						}
					}
					if (thumbnailUrl) {
						indicator.style.backgroundImage = 'url("' + thumbnailUrl + '")'
					}
				}
				if (title) {
					indicator.title = title
				}
				indicator.setAttribute('role', 'link')
				return indicator
			},

			addIndicator: function (index) {
				if (this.indicatorContainer.length) {
					var indicator = this.createIndicator(this.list[index])
					indicator.setAttribute('data-index', index)
					this.indicatorContainer[0].appendChild(indicator)
					this.indicators.push(indicator)
				}
			},

			setActiveIndicator: function (index) {
				if (this.indicators) {
					if (this.activeIndicator) {
						this.activeIndicator.removeClass(this.options.activeIndicatorClass)
					}
					this.activeIndicator = $(this.indicators[index])
					this.activeIndicator.addClass(this.options.activeIndicatorClass)
				}
			},

			initSlides: function (reload) {
				if (!reload) {
					this.indicatorContainer = this.container.find(
						this.options.indicatorContainer
					)
					if (this.indicatorContainer.length) {
						this.indicatorPrototype = document.createElement('li')
						this.indicators = this.indicatorContainer[0].children
					}
				}
				initSlides.call(this, reload)
			},

			addSlide: function (index) {
				addSlide.call(this, index)
				this.addIndicator(index)
			},

			resetSlides: function () {
				resetSlides.call(this)
				this.indicatorContainer.empty()
				this.indicators = []
			},

			handleClick: function (event) {
				var target = event.target || event.srcElement
				var parent = target.parentNode
				if (parent === this.indicatorContainer[0]) {
					// Click on indicator element
					this.preventDefault(event)
					this.slide(this.getNodeIndex(target))
				} else if (parent.parentNode === this.indicatorContainer[0]) {
					// Click on indicator child element
					this.preventDefault(event)
					this.slide(this.getNodeIndex(parent))
				} else {
					return handleClick.call(this, event)
				}
			},

			handleSlide: function (oldIndex, newIndex) {
				handleSlide.call(this, oldIndex, newIndex)
				this.setActiveIndicator(newIndex)
			},

			handleClose: function () {
				if (this.activeIndicator) {
					this.activeIndicator.removeClass(this.options.activeIndicatorClass)
				}
				handleClose.call(this)
			}
		})

		return Gallery
	})
	/*
	 * blueimp Gallery Fullscreen JS
	 * https://github.com/blueimp/Gallery
	 *
	 * Copyright 2013, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * https://opensource.org/licenses/MIT
	 */

	/* global define */

	; (function (factory) {
		'use strict'
		if (typeof define === 'function' && define.amd) {
			// Register as an anonymous AMD module:
			define(['./blueimp-helper', './blueimp-gallery'], factory)
		} else {
			// Browser globals:
			factory(window.blueimp.helper || window.jQuery, window.blueimp.Gallery)
		}
	})(function ($, Gallery) {
		'use strict'

		var galleryPrototype = Gallery.prototype

		$.extend(galleryPrototype.options, {
			// Defines if the gallery should open in fullscreen mode:
			fullscreen: false
		})

		var initialize = galleryPrototype.initialize
		var close = galleryPrototype.close

		$.extend(galleryPrototype, {
			getFullScreenElement: function () {
				return (
					document.fullscreenElement ||
					document.webkitFullscreenElement ||
					document.mozFullScreenElement ||
					document.msFullscreenElement
				)
			},

			requestFullScreen: function (element) {
				if (element.requestFullscreen) {
					element.requestFullscreen()
				} else if (element.webkitRequestFullscreen) {
					element.webkitRequestFullscreen()
				} else if (element.mozRequestFullScreen) {
					element.mozRequestFullScreen()
				} else if (element.msRequestFullscreen) {
					element.msRequestFullscreen()
				}
			},

			exitFullScreen: function () {
				if (document.exitFullscreen) {
					document.exitFullscreen()
				} else if (document.webkitCancelFullScreen) {
					document.webkitCancelFullScreen()
				} else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen()
				} else if (document.msExitFullscreen) {
					document.msExitFullscreen()
				}
			},

			initialize: function () {
				initialize.call(this)
				if (this.options.fullscreen && !this.getFullScreenElement()) {
					this.requestFullScreen(this.container[0])
				}
			},

			close: function () {
				if (this.getFullScreenElement() === this.container[0]) {
					this.exitFullScreen()
				}
				close.call(this)
			}
		})

		return Gallery
	})

	/*
	 * blueimp Gallery Video Factory JS
	 * https://github.com/blueimp/Gallery
	 *
	 * Copyright 2013, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * https://opensource.org/licenses/MIT
	 */

	/* global define */

	; (function (factory) {
		'use strict'
		if (typeof define === 'function' && define.amd) {
			// Register as an anonymous AMD module:
			define(['./blueimp-helper', './blueimp-gallery'], factory)
		} else {
			// Browser globals:
			factory(window.blueimp.helper || window.jQuery, window.blueimp.Gallery)
		}
	})(function ($, Gallery) {
		'use strict'

		var galleryPrototype = Gallery.prototype

		$.extend(galleryPrototype.options, {
			// The class for video content elements:
			videoContentClass: 'video-content',
			// The class for video when it is loading:
			videoLoadingClass: 'video-loading',
			// The class for video when it is playing:
			videoPlayingClass: 'video-playing',
			// The class for video content displayed in an iframe:
			videoIframeClass: 'video-iframe',
			// The class for the video cover element:
			videoCoverClass: 'video-cover',
			// The class for the video play control:
			videoPlayClass: 'video-play',
			// The class for the video play control:
			videoControlClass: 'video-control',
			// Play videos automaticaly:
			videoAutostart: false,
			// Play videos inline by default:
			videoPlaysInline: true,
			// The list object property (or data attribute) for video preload:
			videoPreloadProperty: 'preload',
			// The list object property (or data attribute) for the video poster URL:
			videoPosterProperty: 'poster',
		})

		var handleSlide = galleryPrototype.handleSlide

		$.extend(galleryPrototype, {
			handleSlide: function (oldIndex, newIndex) {
				handleSlide.call(this, oldIndex, newIndex)
				this.setTimeout(function () {
					if (this.activeVideo) {
						this.activeVideo.pause()
					}
					
					if(this.options.videoAutostart || (oldIndex === newIndex)){
						$(this.slides[newIndex]).find('.' + this.options.videoContentClass).trigger('selected');
					}
				});
			},

			videoFactory: function (obj, callback, videoInterface) {
				var that = this
				var options = this.options
				var videoContainerNode = this.elementPrototype.cloneNode(false)
				var videoContainer = $(videoContainerNode)
				var errorArgs = [
					{
						type: 'error',
						target: videoContainerNode
					}
				]
				var video = videoInterface || document.createElement('video')
				var coverElement = this.elementPrototype.cloneNode(false)
				var playElement = document.createElement('a')
				var url = this.getItemProperty(obj, options.urlProperty)
				var sources = this.getItemProperty(obj, options.sourcesProperty)
				var title = this.getItemProperty(obj, options.titleProperty)
				var posterUrl = this.getItemProperty(obj, options.videoPosterProperty)
				var playControls = [playElement]
				var hasGalleryControls
				var isLoading
				var i
				videoContainer.addClass(options.videoContentClass)
				$(playElement).addClass(options.videoPlayClass)
				if (
					!$(coverElement)
						.addClass(options.videoCoverClass)
						.hasClass(options.toggleClass)
				) {
					playControls.push(coverElement)
				}
				coverElement.draggable = false
				if (title) {
					videoContainerNode.title = title
					playElement.setAttribute('aria-label', title)
				}
				if (posterUrl) {
					// Set as background image instead of as poster video element property:
					// - Is accessible for browsers that do not support the video element
					// - Is accessible for both video element and iframe video players
					// - Avoids visual artifacts in IE with the poster property set
					coverElement.style.backgroundImage = 'url("' + posterUrl + '")'
				}
				if (video.setAttribute) {
					if (options.videoPlaysInline) video.setAttribute('playsinline', '')
				} else {
					videoContainer.addClass(options.videoIframeClass)
				}
				video.preload =
					this.getItemProperty(obj, options.videoPreloadProperty) || 'none'
				if (this.support.source && sources) {
					for (i = 0; i < sources.length; i += 1) {
						video.appendChild(
							$.extend(this.sourcePrototype.cloneNode(false), sources[i])
						)
					}
				}
				if (url) video.src = url
				playElement.href = url || (sources && sources.length && sources[0].src)
				if (video.play && video.pause) {
					; (videoInterface || $(video))
						.on('error', function () {
							that.setTimeout(callback, errorArgs)
						})
						.on('pause', function () {
							if (video.seeking) return
							isLoading = false
							videoContainer
								.removeClass(that.options.videoLoadingClass)
								.removeClass(that.options.videoPlayingClass)
							if (hasGalleryControls) {
								that.container.addClass(that.options.controlsClass)
							}
							video.controls = false
							if (video === that.activeVideo) delete that.activeVideo
							if (that.interval) {
								// Continue slideshow interval
								that.play()
							}
						})
						.on('playing', function () {
							isLoading = false
							coverElement.removeAttribute('style')
							videoContainer
								.removeClass(that.options.videoLoadingClass)
								.addClass(that.options.videoPlayingClass)
						})
						.on('play', function () {
							// Clear slideshow timeout:
							window.clearTimeout(that.timeout)
							isLoading = true
							videoContainer.addClass(that.options.videoLoadingClass)
							if (that.container.hasClass(that.options.controlsClass)) {
								hasGalleryControls = true
								that.container.removeClass(that.options.controlsClass)
							} else {
								hasGalleryControls = false
							}
							video.controls = true
							that.activeVideo = video
						})
					$(videoContainer).on('click', function (event) {
						var target = event.target || event.srcElement
						if ((''+target.classList).indexOf(that.options.videoControlClass) === -1) {
							that.preventDefault(event)

							if (isLoading || video === that.activeVideo) {
								video.pause()
							} else {
								video.play()
							}
						}
					})
					videoContainerNode.appendChild(
						(videoInterface && videoInterface.element) || video
					)
				}
				
				if (video.playOnReady) {
					$(videoContainerNode).on('selected', function (event) {
						if(!video.hasPlayed){							
							video.play();
						}
					});
				}

				videoContainerNode.appendChild(coverElement)
				videoContainerNode.appendChild(playElement)

				this.setTimeout(callback, [
					{
						type: 'load',
						target: videoContainerNode
					}
				])
				return videoContainerNode
			}
		})

		return Gallery
	})

	/*
	 * blueimp Gallery YouTube Video Factory JS
	 * https://github.com/blueimp/Gallery
	 *
	 * Copyright 2013, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * https://opensource.org/licenses/MIT
	 */

	/* global define, YT */

	; (function (factory) {
		'use strict'
		if (typeof define === 'function' && define.amd) {
			// Register as an anonymous AMD module:
			define(['./blueimp-helper', './blueimp-gallery-video'], factory)
		} else {
			// Browser globals:
			factory(window.blueimp.helper || window.jQuery, window.blueimp.Gallery)
		}
	})(function ($, Gallery) {
		'use strict'

		if (!window.postMessage) {
			return Gallery
		}

		var galleryPrototype = Gallery.prototype

		$.extend(galleryPrototype.options, {
			// The list object property (or data attribute) with the YouTube video id:
			youTubeVideoIdProperty: 'youtube',
			youTubeContentClass: 'youtube-content',
			youTubeWrapClass: 'youtube-wrap',
			// Optional object with parameters passed to the YouTube video player:
			// https://developers.google.com/youtube/player_parameters
			youTubePlayerVars: {
				color: 'white',
				wmode: 'transparent',
				listType: 'playlist',
				modestbranding: 1,
				playsinline: 1,
				controls: 0,				
				loop: 1,
				rel: 0,
			},
			// Require a click on the native YouTube player for the initial playback:
			youTubeClickToPlay: false,
			playOnReady: true
		})

		var textFactory =
			galleryPrototype.textFactory || galleryPrototype.imageFactory
		var YouTubePlayer = function (videoId, options) {
			this.options = $.extend({}, options)
			this.videoId = videoId
			this.playerVars = $.extend({ playlist: videoId }, options.youTubePlayerVars)
			this.playOnReady = options.playOnReady
			this.clickToPlay = options.youTubeClickToPlay
			this.element = document.createElement('div')
			this.listeners = {}
			this.retryCount = 0
		}

		$.extend(YouTubePlayer.prototype, {
			on: function (type, func) {
				this.listeners[type] = func
				return this
			},

			loadAPI: function () {
				var that = this
				var onYouTubeIframeAPIReady = window.onYouTubeIframeAPIReady
				var apiUrl = 'https://www.youtube.com/iframe_api'
				var scriptTags = document.getElementsByTagName('script')
				var i = scriptTags.length
				var scriptTag
				window.onYouTubeIframeAPIReady = function () {
					if (onYouTubeIframeAPIReady) {
						onYouTubeIframeAPIReady.apply(this)
					}
					if (that.playOnReady) {
						that.play()
					}
				}
				while (i) {
					i -= 1
					if (scriptTags[i].src === apiUrl) {
						return
					}
				}
				scriptTag = document.createElement('script')
				scriptTag.src = apiUrl
				scriptTags[0].parentNode.insertBefore(scriptTag, scriptTags[0])
			},

			onReady: function () {
				var that = this
				this.ready = true
				
				this.videoNav.min=0
				this.videoNav.max=this.player.getDuration()
				this.videoNav.addEventListener('change', function(e) {
					clearInterval(that.tick)
					that.tick = setInterval(that.onTick, 1000, that);
					
					that.player.seekTo(e.target.value)
				})
				
				this.tick = setInterval(this.onTick, 1000, this);
				
				if (this.playOnReady) {
					this.play()
				}
			},
			
			onTick: function (self) {
				self.videoNav.value=self.player.getCurrentTime()
			},

			onPlaying: function () {
				if (this.playStatus < 2) {
					this.listeners.playing()
					this.playStatus = 2
				}
			},

			onPause: function () {
				this.listeners.pause()
				delete this.playStatus
			},

			onStateChange: function (event) {
				window.clearTimeout(this.pauseTimeout)
				switch (event.data) {
					case YT.PlayerState.PLAYING:
						this.hasPlayed = true
						this.onPlaying()
						break
					case YT.PlayerState.UNSTARTED:
					case YT.PlayerState.PAUSED:
						// YouTube sends an unstarted event if pause is triggered before the
						// video has started.
						// YouTube sends a pause event when seeking.
						// In both cases, we initiate a pause in a timeout that gets cleared
						// if followed by another event within the timeout window.
						this.pauseTimeout = galleryPrototype.setTimeout.call(
							this,
							this.onPause,
							null,
							500
						)
						break
					case YT.PlayerState.ENDED:
						this.onPause()
						break
				}
			},

			onError: function (event) {
				this.listeners.error(event)
			},

			play: function () {
				var that = this
				if (!this.playStatus) {
					this.listeners.play()
					this.playStatus = 1
				}
				if (this.ready) {
					if (
						!this.hasPlayed &&
						(this.clickToPlay ||
							(window.navigator &&
								/iP(hone|od|ad)/.test(window.navigator.platform)))
					) {
						// Manually trigger the playing callback if clickToPlay
						// is enabled and to workaround a limitation in iOS,
						// which requires synchronous user interaction to start
						// the video playback:
						this.onPlaying()
					} else {
						this.player.playVideo()
					}
				} else {
					this.playOnReady = true
					if (!(window.YT && YT.Player)) {
						this.loadAPI()
					} else if (!this.player) {
						var videoWrap = document.createElement('div'),
							videoContent = this.element.parentNode;

						videoWrap.appendChild(this.element);
						videoWrap.className = this.options.youTubeWrapClass;
						videoContent.insertBefore(videoWrap, videoContent.firstChild);

						this.player = new YT.Player(this.element, {
							videoId: this.videoId,
							playerVars: this.playerVars,
							events: {
								onReady: function () {
									setTimeout(function(){
										that.onReady()
									}, 300);
								},
								onStateChange: function (event) {
									that.onStateChange(event)
								},
								onError: function (event) {
									if(that.retryCount < 3){
										that.retryCount++;
										that.player.loadVideoById(that.videoId)
									}else{
										that.onError(event)
									}
								}
							}
						})
						
						this.videoNav = document.createElement('input')
						this.videoNav.classList.add(this.options.videoControlClass)
						this.videoNav.type="range"
						this.videoNav.value=0
						this.videoNav.draggable = false
						
						videoContent.insertBefore(this.videoNav, videoContent.firstChild.nextSibling);						
					}
				}
			},

			pause: function () {
				if (this.ready) {
					this.player.pauseVideo()
				} else if (this.playStatus) {
					delete this.playOnReady
					this.listeners.pause()
					delete this.playStatus
				}
			}
		})

		var handleClick = galleryPrototype.handleClick,
			onmousedown = galleryPrototype.onmousedown

		$.extend(galleryPrototype, {
			YouTubePlayer: YouTubePlayer,
			
			handleClick: function (event) {
				var target = event.target || event.srcElement
				if ((''+target.classList).indexOf(this.options.videoControlClass) === -1) {
					return handleClick.call(this, event)
				}
			},
			
			onmousedown: function (event) {
				var target = event.target || event.srcElement
				if ((''+target.classList).indexOf(this.options.videoControlClass) === -1) {
					return onmousedown.call(this, event)
				}
			},

			textFactory: function (obj, callback) {
				var options = this.options
				var videoId = this.getItemProperty(obj, options.youTubeVideoIdProperty)
				if (videoId) {
					if (this.getItemProperty(obj, options.urlProperty) === undefined) {
						obj[options.urlProperty] =
							'https://www.youtube.com/watch?v=' + videoId
					}
					if (
						this.getItemProperty(obj, options.videoPosterProperty) === undefined
					) {
						obj[options.videoPosterProperty] =
							'https://img.youtube.com/vi/' + videoId + '/maxresdefault.jpg'
					}

					var videoContent = this.videoFactory(
						obj,
						callback,
						new YouTubePlayer(
							videoId,
							options
						)
					)
					videoContent.classList.add(options.youTubeContentClass);

					var img = new Image();
					img.onload = function (e) {
						if (img.height == 90) {
							var fallback_image = 'https://img.youtube.com/vi/' + videoId + '/0.jpg';

							$(obj).attr(options.videoPosterProperty, fallback_image);
							$(videoContent).children('.video-cover').css('background-image', 'url(' + fallback_image + ')');
						}

						img = null;
					};
					img.src = obj[options.videoPosterProperty];

					return videoContent;
				}
				return textFactory.call(this, obj, callback)
			}
		})

		return Gallery
	})

	/*
	 * blueimp Gallery Vimeo Video Factory JS
	 * https://github.com/blueimp/Gallery
	 *
	 * Copyright 2013, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * https://opensource.org/licenses/MIT
	 */

	/* global define, $f */

	; (function (factory) {
		'use strict'
		if (typeof define === 'function' && define.amd) {
			// Register as an anonymous AMD module:
			define(['./blueimp-helper', './blueimp-gallery-video'], factory)
		} else {
			// Browser globals:
			factory(window.blueimp.helper || window.jQuery, window.blueimp.Gallery)
		}
	})(function ($, Gallery) {
		'use strict'

		if (!window.postMessage) {
			return Gallery
		}

		var galleryPrototype = Gallery.prototype

		$.extend(galleryPrototype.options, {
			// The list object property (or data attribute) with the Vimeo video id:
			vimeoVideoIdProperty: 'vimeo',
			// The URL for the Vimeo video player, can be extended with custom parameters:
			// https://developer.vimeo.com/player/embedding
			vimeoPlayerUrl:
				'https://player.vimeo.com/video/VIDEO_ID?api=1&player_id=PLAYER_ID',
			// The prefix for the Vimeo video player ID:
			vimeoPlayerIdPrefix: 'vimeo-player-',
			// Require a click on the native Vimeo player for the initial playback:
			vimeoClickToPlay: false
		})

		var textFactory =
			galleryPrototype.textFactory || galleryPrototype.imageFactory
		var VimeoPlayer = function (url, videoId, playerId, clickToPlay) {
			this.url = url
			this.videoId = videoId
			this.playerId = playerId
			this.clickToPlay = clickToPlay
			this.element = document.createElement('div')
			this.listeners = {}
		}
		var counter = 0

		$.extend(VimeoPlayer.prototype, {
			on: function (type, func) {
				this.listeners[type] = func
				return this
			},

			loadAPI: function () {
				var that = this
				var apiUrl = 'https://f.vimeocdn.com/js/froogaloop2.min.js'
				var scriptTags = document.getElementsByTagName('script')
				var i = scriptTags.length
				var scriptTag
				var called
				/**
				 * Callback function
				 */
				function callback() {
					if (!called && that.playOnReady) {
						that.play()
					}
					called = true
				}
				while (i) {
					i -= 1
					if (scriptTags[i].src === apiUrl) {
						scriptTag = scriptTags[i]
						break
					}
				}
				if (!scriptTag) {
					scriptTag = document.createElement('script')
					scriptTag.src = apiUrl
				}
				$(scriptTag).on('load', callback)
				scriptTags[0].parentNode.insertBefore(scriptTag, scriptTags[0])
				// Fix for cached scripts on IE 8:
				if (/loaded|complete/.test(scriptTag.readyState)) {
					callback()
				}
			},

			onReady: function () {
				var that = this
				this.ready = true
				this.player.addEvent('play', function () {
					that.hasPlayed = true
					that.onPlaying()
				})
				this.player.addEvent('pause', function () {
					that.onPause()
				})
				this.player.addEvent('finish', function () {
					that.onPause()
				})
				
				if (this.playOnReady) {
					this.play()
				}
			},

			onPlaying: function () {
				if (this.playStatus < 2) {
					this.listeners.playing()
					this.playStatus = 2
				}
			},

			onPause: function () {
				this.listeners.pause()
				delete this.playStatus
			},

			insertIframe: function () {
				var iframe = document.createElement('iframe')
				iframe.src = this.url
					.replace('VIDEO_ID', this.videoId)
					.replace('PLAYER_ID', this.playerId)
				iframe.id = this.playerId
				iframe.allow = 'autoplay'
				this.element.parentNode.replaceChild(iframe, this.element)
				this.element = iframe
			},

			play: function () {
				var that = this
				if (!this.playStatus) {
					this.listeners.play()
					this.playStatus = 1
				}
				if (this.ready) {
					if (
						!this.hasPlayed &&
						(this.clickToPlay ||
							(window.navigator &&
								/iP(hone|od|ad)/.test(window.navigator.platform)))
					) {
						// Manually trigger the playing callback if clickToPlay
						// is enabled and to workaround a limitation in iOS,
						// which requires synchronous user interaction to start
						// the video playback:
						this.onPlaying()
					} else {
						this.player.api('play')
					}
				} else {
					this.playOnReady = true
					if (!window.$f) {
						this.loadAPI()
					} else if (!this.player) {
						this.insertIframe()
						this.player = $f(this.element)
						this.player.addEvent('ready', function () {
							that.onReady()
						})
					}
				}
			},

			pause: function () {
				if (this.ready) {
					this.player.api('pause')
				} else if (this.playStatus) {
					delete this.playOnReady
					this.listeners.pause()
					delete this.playStatus
				}
			}
		})

		$.extend(galleryPrototype, {
			VimeoPlayer: VimeoPlayer,

			textFactory: function (obj, callback) {
				var options = this.options
				var videoId = this.getItemProperty(obj, options.vimeoVideoIdProperty)
				if (videoId) {
					if (this.getItemProperty(obj, options.urlProperty) === undefined) {
						obj[options.urlProperty] = 'https://vimeo.com/' + videoId
					}
					counter += 1
					return this.videoFactory(
						obj,
						callback,
						new VimeoPlayer(
							options.vimeoPlayerUrl,
							videoId,
							options.vimeoPlayerIdPrefix + counter,
							options.vimeoClickToPlay
						)
					)
				}
				return textFactory.call(this, obj, callback)
			}
		})

		return Gallery
	})

	/*
	 * blueimp Gallery jQuery plugin
	 * https://github.com/blueimp/Gallery
	 *
	 * Copyright 2013, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * https://opensource.org/licenses/MIT
	 */

	/* global define */

	; (function (factory) {
		'use strict'
		if (typeof define === 'function' && define.amd) {
			define(['jquery', './blueimp-gallery'], factory)
		} else {
			factory(window.jQuery, window.blueimp.Gallery)
		}
	})(function ($, Gallery) {
		'use strict'

		// Global click handler to open links with data-gallery attribute
		// in the Gallery lightbox:
		$(document).on('click', '[data-gallery]', function (event) {
			// Get the container id from the data-gallery attribute:
			var id = $(this).data('gallery')
			var widget = $(id)
			var container =
				(widget.length && widget) || $(Gallery.prototype.options.container)
			var callbacks = {
				onopen: function () {
					container.data('gallery', this).trigger('open')
				},
				onopened: function () {
					container.trigger('opened')
				},
				onslide: function () {
					container.trigger('slide', arguments)
				},
				onslideend: function () {
					container.trigger('slideend', arguments)
				},
				onslidecomplete: function () {
					container.trigger('slidecomplete', arguments)
				},
				onclose: function () {
					container.trigger('close')
				},
				onclosed: function () {
					container.trigger('closed').removeData('gallery')
				}
			}
			var options = $.extend(
				// Retrieve custom options from data-attributes
				// on the Gallery widget:
				container.data(),
				{
					container: container[0],
					index: this,
					event: event
				},
				callbacks
			)
			// Select all links with the same data-gallery attribute:
			var links = $(this)
				.closest('[data-gallery-group], body')
				.find('[data-gallery="' + id + '"]')
			if (options.filter) {
				links = links.filter(options.filter)
			}
			return new Gallery(links, options)
		})
	})
