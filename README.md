# How to

1. Edit .env to change API_KEY/API_SECRET
2. [Optional]: edit also RETURN_URL, a working URL is not required
3. Run following bash commands:

```bash
# install depedencies
composer install --no-dev

# create a payment request : the script will provide a hosted checkout URL
# see also credit card test case data to use in the script output
php 01-test-ingenico-create-hosted-checkout.php

# after completing payment on the hosted checkout form run this script
# you will need the <hosted-checkout-id-from-the-previous-step>
02-test-ingenico-get-status.php 'hosted-checkout-id-from-the-previous-step'

# if payment status is in PENDING_APPROVAL run next script
# you will need the the <payment-id> from the previous steps
php 03-test-ingenico-approve.php <payment-id>

```
