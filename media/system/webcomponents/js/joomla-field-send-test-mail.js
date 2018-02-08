((customElements, Joomla) => {

	class JoomlaFieldSendTestMail extends HTMLElement {

	constructor() {
		super();

		if (!Joomla) {
			throw new Error('Joomla API is not loaded')
		}

		if (!this.getAttribute('uri')) {
			throw new Error('No valid url for validation')
		}
	}

	connectedCallback() {
		const self = this;
		const button = document.getElementById('sendtestmail');

		if (button) {
			button.addEventListener('click', () => { self.sendTestMail(self); });
		}
	}

	sendTestMail() {
		const email_data = {
			smtpauth: this.querySelector('[name="jform[smtpauth]"]').value,
			smtpuser: this.querySelector('[name="jform[smtpuser]"]').value,
			smtppass: this.querySelector('[name="jform[smtppass]"]').value,
			smtphost: this.querySelector('[name="jform[smtphost]"]').value,
			smtpsecure: this.querySelector('[name="jform[smtpsecure]"]').value,
			smtpport: this.querySelector('[name="jform[smtpport]"]').value,
			mailfrom: this.querySelector('[name="jform[mailfrom]"]').value,
			fromname: this.querySelector('[name="jform[fromname]"]').value,
			mailer: this.querySelector('[name="jform[mailer]"]').value,
			mailonline: this.querySelector('[name="jform[mailonline]"]').value,
		};

		// Remove js messages, if they exist.
		Joomla.removeMessages();

		Joomla.request({
			url: this.getAttribute('uri'),
			method: 'POST',
			data: JSON.stringify(email_data),
			perform: true,
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			onSuccess: (response, xhr) => {
				response = JSON.parse(response);
				if (typeof response.messages === 'object' && response.messages !== null) {
					Joomla.renderMessages(response.messages);
				}
			},
			onError: (xhr) => {
				Joomla.renderMessages(Joomla.ajaxErrorsMessages(xhr));
			},
		});
	}
}

customElements.define('joomla-field-send-test-mail', JoomlaFieldSendTestMail);

})(customElements, Joomla);
