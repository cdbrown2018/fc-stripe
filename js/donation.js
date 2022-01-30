successSlug = "";
globalSubscriptionId = null;

function handleErrors(response) {
    if (!response.ok) {
        throw Error(response.statusText);
    }
    return response;
}

window.addEventListener('load', () => {
    document
        .querySelector("#payment-form")
        .addEventListener("submit", handleSubmit);

    document
        .querySelector("#define-amount")
        .addEventListener("submit", declareAmount);

    let testMode = !!my_options.test_mode;
    let testKey = my_options.test_key;
    let prodKey = my_options.prod_key;
    successSlug = my_options.success_slug;

    if (!!testMode) document.getElementById('stripe-donation-model').innerHTML += '<h3>Test mode: ACTIVE</h3>';
    const stripe = Stripe(testMode ? testKey : prodKey);

    let elements;

    async function initialize() {

        const formData = new FormData(document.getElementById('define-amount'));

        const formDataObj = {};
        for (var pair of formData.entries()) {
            if (pair[0] != 'amount') {
                formDataObj[pair[0]] = pair[1];
            }
        }
        const parsedAmount = (Number.parseFloat(document.getElementById('amount').value) * 100);
        const {
            clientSecret,
            subscriptionId
        } = await fetch("/wp-json/fc-stripe/v1/process_donation?amount=" + parsedAmount + "&isrecurring=false", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formDataObj),
        }).then((r) => r.json());

        globalSubscriptionId = subscriptionId;
        elements = stripe.elements({
            clientSecret
        });

        document.getElementById('define-amount').classList.add('fcs-hidden');
        document.getElementsByClassName('fcs-stripe-submit-button')[0].classList.remove('fcs-hidden');
        document.getElementById('confirmation-message').innerHTML += 'Thank you for choosing to donate $' + (parsedAmount / 100) + '!<br />If you would like to change this amount, please refresh the page.';
        const paymentElement = elements.create("payment");
        paymentElement.mount("#payment-element");
    }
    async function handleSubmit(e) {
        e.preventDefault();
        setLoading(true);

        returnUrl = document.location.origin + "/" + successSlug + (globalSubscriptionId != null ? "?subscriptionId=" + globalSubscriptionId : "");

        const {
            error
        } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                // Make sure to change this to your payment completion page
                return_url: returnUrl,
            },
        });

        if (error.type === "card_error" || error.type === "validation_error") {
            showMessage(error.message);
        } else {
            showMessage("An unexpected error occured.");
        }

        setLoading(false);
        return false;
    }

    async function checkStatus() {
        const clientSecret = new URLSearchParams(window.location.search).get(
            "payment_intent_client_secret"
        );

        if (!clientSecret) {
            return;
        }

        const {
            paymentIntent
        } = await stripe.retrievePaymentIntent(clientSecret);

        switch (paymentIntent.status) {
            case "succeeded":
                showMessage("Payment succeeded!");
                break;
            case "processing":
                showMessage("Your payment is processing.");
                break;
            case "requires_payment_method":
                showMessage("Your payment was not successful, please try again.");
                break;
            default:
                showMessage("Something went wrong.");
                break;
        }
    }

    async function declareAmount() {
        initialize();
        checkStatus();
        return false;
    }

    // ------- UI helpers -------

    function showMessage(messageText) {
        const messageContainer = document.querySelector("#payment-message");

        messageContainer.classList.remove("hidden");
        messageContainer.textContent = messageText;

        setTimeout(function () {
            messageContainer.classList.add("hidden");
            messageText.textContent = "";
        }, 4000);
    }

    // Show a spinner on payment submission
    function setLoading(isLoading) {
        if (isLoading) {
            // Disable the button and show a spinner
            document.querySelector("#submit").disabled = true;
            document.querySelector("#spinner").classList.remove("hidden");
            document.querySelector("#button-text").classList.add("hidden");
        } else {
            document.querySelector("#submit").disabled = false;
            document.querySelector("#spinner").classList.add("hidden");
            document.querySelector("#button-text").classList.remove("hidden");
        }
    }

});

window.addEventListener('load', () => {
    document.querySelector('#fcs-cancellation-form')
        .addEventListener('submit', cancel);

    async function cancel(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('fcs-cancellation-form'));
        const formDataObj = {};
        for (var pair of formData.entries()) {
            formDataObj[pair[0]] = pair[1];
        }

        await fetch("/wp-json/fc-stripe/v1/cancel_subscription", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formDataObj),
        })
        .then(handleErrors)
        .then((r) => {
            document.getElementById('fcs-cancel-success-prompt').classList.remove("fcs-hidden");
        })
        .catch(() => alert("Looks like there was an issue processing your request. Please try again."));
    }
})