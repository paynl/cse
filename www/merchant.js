import {
    EncryptedForm,
    Events,
    AuthorizingModal,
    ChallengeModal
}  from './cryptography-js/dist/pay-cryptography.js';

export default class MyForm {
    init() {
        let encryptedForm = new EncryptedForm({
            'debug': false,
            'language': 'NL',
            'refresh_url': keyUrl,
            'public_keys': JSON.parse(keyPairs),
            'post_url': 'process.php',
            'status_url': 'transaction-status.php?transaction_id=%transaction_id%',
            'authentication_url': 'authenticate.php',
            'authorization_url': 'authorize.php',
            'payment_complete_url': 'complete.php'
        });

        encryptedForm.init();
        encryptedForm.getEventDispatcher().addListener(
            Events.onBeforeDisplayModalEvent,
            event => MyForm.onBeforeDisplayModalEvent(event)
        );

        window.encryptedForm = encryptedForm;
    }

    /**
     * Wrap custom modal html around the given modal.
     *
     * @param event
     */
    static onBeforeDisplayModalEvent(event) {
        let modal = event.getParameter('modal');
        let body = event.getSubject();

        // add some padding for textual modal responses, only the challenge and authorization modals deviate
        if (!(modal instanceof AuthorizingModal) && !(modal instanceof ChallengeModal)) {
            body = `<div class="padding-text">${body}</div>`;
        }

        event.subject = `<div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container rounded-box" role="dialog" aria-modal="true">
            <header class="modal__header">
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
              ${body}
            </main>
          </div>
        </div>`;
    }
}