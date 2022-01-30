<?php
add_shortcode('fcs_stripe', 'fcs_register_shortcode');
add_shortcode('fcs_customer_id', 'fcs_register_success_shortcode');
add_shortcode('fcs_cancel_subscription', 'fcs_register_cancellation_shortcode');
function fcs_register_shortcode()
{ ?>
    <div id="stripe-donation-model"></div>
    <form id="define-amount" class="fcs-container" onsubmit="return false;">
        <div class="fcs-row">
            <div class="fcs-col-12">
                <p>Most of this information is not required. However, we do ask that you consider filling out this form so we can better understand who our donors are.</p>
            </div>
        </div>
        <div class="grid-full fcs-row">
            <div class="fcs-col-12">
                <label for="amount">Donation Amount *</label>
                <input type="number" id="amount" name="amount" />
            </div>
        </div>
        <div class="grid-full fcs-row" style="display:inline-flex; align-items:center;">
            <div class="fcs-col-12">
                <label for="isrecurring">Make this a recurring donation?</label>
                <input type="checkbox" id="isrecurring" name="isrecurring" style="margin-left: 1em;" />
            </div>
        </div>
        <div class="fcs-row">
            <div class="fcs-col-6 fcs-col-12-sm">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" />
            </div>
            <div class="fcs-col-6 fcs-col-12-sm">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" />
            </div>
        </div>
        <div class="fcs-row">
            <div class="fcs-col-12-sm fcs-col-4">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" />
            </div>
            <div class="fcs-col-12-sm fcs-col-4">
                <label for="telephone">Telephone</label>
                <input type="text" id="telephone" name="telephone" />
            </div>
            <div class="fcs-col-12-sm fcs-col-4">
                <label for="city">City</label>
                <input type="text" id="city" name="city" />
            </div>
        </div>
        <div class="fcs-row">
            <div class="fcs-col-12">
                <button type="submit" style="width: 100%;">Submit</button>
            </div>
        </div>
    </form>
    <form id="payment-form">
        <div class="fcs-container">
            <div class="fcs-row">
                <div class="fcs-col-12">
                    <div id="confirmation-message"></div>
                </div>
            </div>
            <div class="fcs-row">
                <div class="fcs-col-12">
                    <div id="payment-element">
                        <!--Stripe.js injects the Payment Element-->
                    </div>
                    <button id="submit" class="fcs-stripe-submit-button fcs-hidden">
                        <div class="spinner hidden" id="spinner"></div>
                        <span id="button-text">Pay now</span>
                    </button>
                    <div id="payment-message" class="hidden"></div>
                </div>
            </div>
        </div>
    </form>
<?php
}


/*
    This shortcode is simply to provide an easy means of accessing the customer's id. The main intent for this is to give them the option to cancel subscriptions.
*/
function fcs_register_success_shortcode()
{
    $subscriptionId = null;
    if (isset($_GET['subscriptionId'])) {
        $subscriptionId = $_GET['subscriptionId'];
    }

?>
    <div>
        <?php
        if ($subscriptionId != null) {
            echo 'Please save the following key. You will need it later if you wish to cancel your recurring donation.';
            echo '<div><h3>' . $subscriptionId . '</h3></div>';
        }
        ?>
    </div>
<?php
}

function fcs_register_cancellation_shortcode()
{
?>
    <div id="primary" style="width: 100%;" <?php generate_do_element_classes('content'); ?>>
        <main id="main" <?php generate_do_element_classes('main'); ?>>
            <form class="fcs-container" id="fcs-cancellation-form">
                <div class="fcs-row">
                    <div class="fcs-col-12">
                        <label for="subscriptionId">Subscription Id</label>
                        <input type="text" id="subscriptionId" name="subscriptionId" style="width: 100%;" />
                    </div>
                </div>
                <div class="fcs-row">
                    <div class="fcs-col-12">
                        <button type="submit" style="width: 100%;">Submit</button>
                    </div>
                </div>
                <div class="fcs-row fcs-hidden" id="fcs-cancel-success-prompt">
                    <div class="fcs-col-12">
                        <div>
                            Future donations have been successfully cancelled.
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
<?php
}
