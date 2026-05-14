# Direct Bank Transfer Gateway for WHMCS

A custom manual bank transfer payment gateway module for WHMCS invoices.

This module allows customers to pay invoices by direct bank transfer and submit their payment details through a WHMCS support ticket or by email. It is designed for businesses that want a clean, simple, and professional manual bank payment option without using the default WHMCS Bank Transfer gateway.

---

## Overview

**Direct Bank Transfer Gateway for WHMCS** adds a custom payment method to WHMCS invoices.

On the invoice page, the customer sees a clean **Pay with Bank Transfer** button. After clicking the button, a compact popup opens with your bank account details and a payment submission form.

The customer can enter:

- Customer name
- Transaction reference number
- Sender account name or bank name
- Paid amount

After that, the customer can submit the payment details through a WHMCS support ticket or send the payment proof by email.

> This is a manual payment gateway. It does not automatically mark invoices as paid. The admin must verify the payment manually and add the payment to the invoice.

---

## Features

- Custom WHMCS manual bank payment gateway
- Clean invoice button
- Compact responsive payment popup
- Bank account details display
- Copy button for account number
- Customer payment detail collection
- Support ticket submission with pre-filled payment message
- Email proof submission with pre-filled payment message
- Configurable bank details from WHMCS admin panel
- Configurable support department ID
- Configurable business name and support email
- Mobile-friendly layout
- Does not break the WHMCS invoice page design
- Optional logo support

---

## Folder Structure

Upload the module files using the following structure:

```text
modules/
└── gateways/
    ├── directbank.php
    └── directbank/
        ├── logo.png
        ├── whmcs.json
        └── README.txt
