document.addEventListener( 'DOMContentLoaded', () => {
	class Converter {
		/**
		 * Class constructor.
		 */
		constructor() {
			this.CONVERT_FORM_SELECTOR = '#slugify-convert-existing-slugs';
			this.CONVERT_BUTTON_SELECTOR = '#slugify-convert-button';
			this.CONFIRM_POPUP_SELECTOR = '#slugify-confirm-popup';
			this.CONFIRM_OK_SELECTOR = '#slugify-confirm-ok';
			this.CONFIRM_CANCEL_SELECTOR = '#slugify-confirm-cancel';

			this.confirmPopup = document.querySelector(
				this.CONFIRM_POPUP_SELECTOR
			);

			this.bindEvents();
		}

		/**
		 * Bind events to methods.
		 */
		bindEvents() {
			document.querySelector( this.CONVERT_BUTTON_SELECTOR ).onclick = (
				event
			) => {
				event.preventDefault();
				this.confirmPopup.style.display = 'block';
				return false;
			};

			this.confirmPopup.onclick = () => {
				this.hideConfirmPopup();
			};

			document.querySelector( this.CONFIRM_OK_SELECTOR ).onclick = (
				event
			) => {
				event.stopPropagation();
				this.hideConfirmPopup();
				document.querySelector( this.CONVERT_FORM_SELECTOR ).submit();
			};

			document.querySelector( this.CONFIRM_CANCEL_SELECTOR ).onclick = (
				event
			) => {
				event.stopPropagation();
				this.hideConfirmPopup();
			};
		}

		/**
		 * Hide confirmation popup.
		 */
		hideConfirmPopup() {
			this.confirmPopup.style.display = 'none';
		}
	}
	
	new Converter();
} );