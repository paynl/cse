# PAY. CSE Demo Project

This demonstration project consists of the following;

- Latest SDK with the new CSE capable methods present
- A minified version of the javascript library that handles CSE
- Some SASS styling to base off from
- This documentation to get started

The javascript library relies heavily on a custom-built event dispatcher.

## Quick setup

- Make a copy of `config.php.dist` and name it `config.php`, set the configuration values accordingly.
- Run `composer install` to install the relevant dependencies.
- Run `npm install` in case you intend to modify the base SASS files that come with this package.
- Put it somewhere publicly accessible on the web, with the exclusion of the `config.php` file.

## Configuration

The base configuration internally looks like this;

```javascript
{
    'debug': false, // adds information to the console
    'refresh_url': null, // url to obtain the public keys from, in our example /public-keys.php 
    'status_url': null, // url to obtain the 3dsv2 transaction status from, in our example /transaction-status.php
    'authentication_url': null, // url to initially authenticate our transaction, in our example /authenticate.php
    'authorization_url': null, // url to finally authorize our transaction, in our example /authorize.php
    'post_url': null, // url to initially start the transaction and call the first authenticate, in our example /process.php
    'payment_complete_url': null, // url to come back to when payment is complete
    'form_input_payload_name': 'pay_encrypted_data', // do not change
    'form_selector': 'data-pay-encrypt-form', // attribute to look for to identify the target form 
    'field_selector': 'data-pay-encrypt-field', // attribute to look for to identify the target form elements 
    'field_value_reader': 'name', // grabs the required data keys from this attribute
    'public_keys': [], // allows you to feed the current valid public keys in advance, so the client won't have to query these
    'language': 'NL', // language we are using, any validation or succession messages come back in this language
    'merchant_identifier': null, // not in use
    'html5_enhanced_validation': true, // the library enhances base form elements with html5 attributes, you can disble that here 
    'default_cvv_label': 'CVC', // the label changes based on the type of card, it might differ per country which should be the default
    'default_card_number_length': 19, // default card max-length
    'bind': {
        'submit': true
    },
    'icons': {
        'creditcard': {
            'default': '/img/creditcard/cc-front.svg',
            'alipay': '/img/creditcard/cc-alipay.svg',
            'american-express': '/img/creditcard/cc-amex.svg',
            'diners-club': '/img/creditcard/cc-diners-club.svg',
            'discover': '/img/creditcard/cc-discover.svg',
            'elo': '/img/creditcard/cc-elo.svg',
            'hiper': '/img/creditcard/cc-hiper.svg',
            'hipercard': '/img/creditcard/cc-hipercard.svg',
            'jcb': '/img/creditcard/cc-jcb.svg',
            'maestro': '/img/creditcard/cc-maestro.svg',
            'mastercard': '/img/creditcard/cc-mastercard.svg',
            'mir': '/img/creditcard/cc-mir.svg',
            'unionpay': '/img/creditcard/cc-unionpay.svg',
            'visa': '/img/creditcard/cc-visa.svg'
        },
        'cvc': '/img/creditcard/cc-back.svg',
    }
}
```

The `status_url` has a replacement variable for %transaction_id% as this is a GET request, for example:

```javascript
{ status_url : '/transaction-status.php?transaction_id=%transaction_id%'}
``` 

## Form Attributes

You should take notice of the following attributes when inspecting the markup of the form in this demo;

- `data-pay-encrypt-field`, mark the input/select element for data we want to retrieve and encrypt.
- `data-credit-card-type`, mark the image element to replace the image into of a given card when we can recognize the type. 
- `data-cvc-label`, mark a `<label>` element as a holder of the type of security code we have based on the card.
- `data-credit-card-cvc`, mark the image element for the card security code.
- `data-loading-state`, will be applied to the form's button ( `[type="submit"]` ) when the form is either loading or stops loading.

Internally we register the following elements;

- `Elements.creditCardImage` via `img[data-credit-card-type]`
- `Elements.creditCardHolder` via `[name="cardholder"]`
- `Elements.creditCardNumber` via `[name="cardnumber"]`
- `Elements.creditCardCvv` via `[name="cardcvc"]`
- `Elements.creditCardCvvLabel` via `[data-cvc-label]`
- `Elements.creditCardCvvImage` via `img[data-credit-card-cvc]`
- `Elements.creditCardExpiration` via `[name="expiration"]`, _not in use, placeholder for implementation of a single MM/YY field later_.
- `Elements.creditCardExpirationMonth` via `[name="valid_thru_month"]`
- `Elements.creditCardExpirationYear` via `[name="valid_thru_year"]`
- `Elements.modalContainer` via `#payment-modal`
- `Elements.tdsMethodPlaceholderId` via `#payment_tds_method_placeholder`
    - This element appends automatically to the end of the html body.

It is especially important the form input's for the card data stay the same.

If you need to access these elements within an Event Listener, retrieve them as such:

```javascript
State.getInstance().getElementByReference(Elements.creditCardCvv);
```

## State

The State object holds the current state of affairs, as such if you add any custom behavior you might want to have these in the State object as well.

Some examples of how to use this:

```javascript
State.getInstance().setStateParameter('my_custom_key', 'my_custom_value');
State.getInstance().getStateParameter('my_custom_key');
```

I would use these above when initially adding new keys to the state, and from there on make use of the StateChangeEvent to propagate further changes.

```javascript
encryptedForm.getEventDispatcher().dispatch(new StateChangeEvent(element, {
    'state': {loading: true, formSubmitted: true},
}), Events.onStateChangeEvent);
```

This will update several parameters at once, and allows you to look for certain changes within a custom Event Listener.

## Event dispatching

As mentioned before this library depends heavily on event dispatching, you can extend the `EventListener` to create your own listeners.

To create your own events, you will have to extend the `GenericEvent`. 

### CSRF tokens?

If we are posting to a controller endpoint that still requires a CSRF token to be present in the POST request, you can add these quite easily;

```javascript
eventDispatcher.addListener(
    Events.onSubmitDataEvent,
    function(event){
        event.subject.set('csrf_token', document.querySelector('input[name="csrf_token"]').value);
    }
);
```

_By default we do not collect data we do not specifically need, however in some 3rd party implementations this can be troublesome._

### Trigger a 3rd party present fullscreen loader

Another example is for when we would for instance be integrating in a 3rd party ecommerce system, which besides modals also has a separate fullscreen loader:

```javascript
eventDispatcher.addListener(
    Events.onStateChangeEvent,
    function(event){
        if (event.hasParameter('state') && 'loading' in event.getParameter('state')) {
            event.getCurrentState().isLoading() ?
                null: // start loading
                null; // stop loading
        }
    }
);
```

### Custom modal implementation

There's a default modal manager built in, to override this behavior you will have to override 
the behavior for the `OpenModalEvent` and `CloseModalEvent` events.

When overriding these events, the `onBeforeDisplayModalEvent` will not be triggered anymore, and thus
you are responsible for adding all the logic here.

```javascript
eventDispatcher.addListener(
    payCryptography.Events.onModalOpenEvent,
    function(event){
        event.stopPropagation(); // this halts our internals from handling the event
        // custom logic here
    },
    10
);
```

```javascript
eventDispatcher.addListener(
    Events.onModalCloseEvent,
    function(event){
        event.stopPropagation(); // this halts our internals from handling the event
        // custom logic here
    },
    10
);
```

**Important**, you will have to replicate this behavior for when the modal opens or closes on interaction of the user outside of submitting the form;

```javascript
this.config = Object.assign({}, {
    onShow: () => {
        EventDispatcher.getInstance().dispatch(new StateChangeEvent(null, {
            'state': {modalOpen: true}
        }), Events.onStateChangeEvent);
    },
    onClose: () => {
        let modalContainer = State.getInstance().getElementFromReference(Elements.modalContainer);
        let stateUpdates = {modalOpen: false};

        if (State.getInstance().isFormSubmitted()) {
            stateUpdates['formSubmitted'] = false;
        }

        EventDispatcher.getInstance().dispatch(new StateChangeEvent(null, {
            'state': stateUpdates,
        }), Events.onStateChangeEvent);

        EventDispatcher.getInstance().dispatch(new ModalCloseEvent(this, {
            'manual': true
        }), Events.onModalCloseEvent);

        if (typeof modalContainer !== 'undefined') {
            modalContainer.innerHTML = '';
        }
    },
    awaitOpenAnimation: false,
    awaitCloseAnimation: false
}, config);
```

Especially the `manual` parameter that is passed into the `ModalCloseEvent` is important, this will stop the polling in the background if a transaction has been initiated.

Besides that we also clear the content of the modal window to ensure any polling within this iframe is also canceled.

## Exported classes

We export the following classes from the main library for use outside of the library:

```javascript
export {
    ActionableResponseEvent,
    AuthorizingModal,
    ChallengeModal,
    DebugEvent,
    ElementReferences,
    Elements,
    EncryptedForm,
    ErrorModal,
    EventDispatcher,
    EventListener,
    Events,
    FormDisableElementsListener,
    FormSubmissionListener,
    GenericEvent,
    ModalCloseEvent,
    ModalListener,
    ModalOpenEvent,
    PaymentAuthorizationEvent,
    PaymentCanceledEvent,
    PaymentCompleteEvent,
    PaymentCompleteModal,
    PaymentFailedEvent,
    PaymentRequiresChallengeEvent,
    PaymentRequiresTdsMethodEvent,
    PaymentListener,
    PollingResponse,
    PollingResponseEvent,
    PollingListener,
    ResolveModalListener,
    ResponseFactoryEvent,
    ResponseFactoryListener,
    ResponseToJsonListener,
    State,
    StateChangeEvent,
    StateChangeListener,
    ThreeDSTransactionStatus
};
```
