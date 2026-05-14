<?php

/**
 * Direct Bank Transfer Gateway
 *
 * Manual bank transfer payment verification gateway for WHMCS invoices.
 *
 * File: /modules/gateways/directbank.php
 * Developer: LionStar Host
 * Support: support@lionstarhost.com
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function directbank_MetaData()
{
    return [
        'DisplayName' => 'Direct Bank Transfer',
        'APIVersion' => '1.1',
        'gatewayType' => 'Bank',
        'DisableLocalCreditCardInput' => true,
    ];
}

function directbank_config()
{
    return [
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'Direct Bank Transfer',
        ],

        'bankName' => [
            'FriendlyName' => 'Bank Name',
            'Type' => 'text',
            'Size' => '50',
            'Default' => 'Your Bank Name',
        ],

        'accountName' => [
            'FriendlyName' => 'Account Name',
            'Type' => 'text',
            'Size' => '50',
            'Default' => 'LionStar Host',
        ],

        'accountNumber' => [
            'FriendlyName' => 'Account Number',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
        ],

        'branchName' => [
            'FriendlyName' => 'Branch Name',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
        ],

        'routingNumber' => [
            'FriendlyName' => 'Routing Number',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
        ],

        'departmentId' => [
            'FriendlyName' => 'Support Department ID',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '1',
            'Description' => 'Ticket department ID for payment verification',
        ],

        'businessName' => [
            'FriendlyName' => 'Business Name',
            'Type' => 'text',
            'Size' => '50',
            'Default' => 'LionStar Host',
        ],

        'supportEmail' => [
            'FriendlyName' => 'Support Email',
            'Type' => 'text',
            'Size' => '50',
            'Default' => 'support@lionstarhost.com',
        ],

        'verificationNote' => [
            'FriendlyName' => 'Verification Note',
            'Type' => 'text',
            'Size' => '100',
            'Default' => 'Your invoice will be marked as paid after manual verification.',
        ],
    ];
}

function directbank_html($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function directbank_link($params)
{
    $invoiceId = $params['invoiceid'] ?? '';
    $amount = $params['amount'] ?? '';
    $currency = $params['currency'] ?? '';

    $bankName = !empty($params['bankName']) ? $params['bankName'] : 'Your Bank Name';
    $accountName = !empty($params['accountName']) ? $params['accountName'] : 'LionStar Host';
    $accountNumber = !empty($params['accountNumber']) ? $params['accountNumber'] : '';
    $branchName = !empty($params['branchName']) ? $params['branchName'] : '';
    $routingNumber = !empty($params['routingNumber']) ? $params['routingNumber'] : '';
    $departmentId = !empty($params['departmentId']) ? $params['departmentId'] : 1;
    $businessName = !empty($params['businessName']) ? $params['businessName'] : 'LionStar Host';
    $supportEmail = !empty($params['supportEmail']) ? $params['supportEmail'] : 'support@lionstarhost.com';

    $verificationNote = !empty($params['verificationNote'])
        ? $params['verificationNote']
        : 'Your invoice will be marked as paid after manual verification.';

    $safeInvoiceId = intval($invoiceId);
    $modalId = 'directbankModal_' . $safeInvoiceId;

    $ticketSubject = 'Bank Payment Verification - Invoice ' . $invoiceId;

    $ticketUrlBase = 'submitticket.php?step=2'
        . '&deptid=' . rawurlencode($departmentId)
        . '&subject=' . rawurlencode($ticketSubject);

    $logoPath = 'modules/gateways/directbank/logo.png';

    $branchRow = '';
    if (!empty($branchName)) {
        $branchRow = '
            <tr>
                <td style="padding:4px 0;color:#555555;font-weight:700;">Branch</td>
                <td style="padding:4px 0;color:#111111;">' . directbank_html($branchName) . '</td>
            </tr>';
    }

    $routingRow = '';
    if (!empty($routingNumber)) {
        $routingRow = '
            <tr>
                <td style="padding:4px 0;color:#555555;font-weight:700;">Routing No.</td>
                <td style="padding:4px 0;color:#111111;">' . directbank_html($routingNumber) . '</td>
            </tr>';
    }

    $jsTicketUrlBase = json_encode($ticketUrlBase);
    $jsSupportEmail = json_encode($supportEmail);
    $jsTicketSubject = json_encode($ticketSubject);
    $jsInvoiceId = json_encode((string) $invoiceId);
    $jsAmount = json_encode((string) $amount);
    $jsCurrency = json_encode((string) $currency);

    return '
    <div style="text-align:right;margin-top:8px;">
        <button type="button"
            onclick="document.getElementById(\'' . directbank_html($modalId) . '\').style.display=\'flex\'"
            class="btn btn-primary"
            style="background:#0b4f8a;border-color:#0b4f8a;color:#ffffff;padding:8px 15px;border-radius:6px;font-size:14px;font-weight:700;box-shadow:0 3px 8px rgba(11,79,138,0.25);">
            Pay with Bank Transfer
        </button>
    </div>

    <div id="' . directbank_html($modalId) . '" style="display:none;position:fixed;z-index:99999;left:0;top:0;width:100%;height:100%;background:rgba(15,15,15,0.60);align-items:center;justify-content:center;padding:10px;box-sizing:border-box;overflow-y:auto;">

        <div style="background:#ffffff;width:100%;max-width:390px;border-radius:12px;overflow:hidden;box-shadow:0 10px 32px rgba(0,0,0,0.25);font-family:Arial,sans-serif;margin:auto;">

            <div style="background:#0b4f8a;color:#ffffff;padding:11px 14px;display:flex;justify-content:space-between;align-items:center;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <img src="' . directbank_html($logoPath) . '" alt="Bank Transfer" style="max-height:28px;max-width:110px;background:#ffffff;border-radius:4px;padding:2px;" onerror="this.style.display=\'none\';">
                    <div>
                        <h3 style="margin:0;color:#ffffff;font-size:16px;font-weight:800;">Bank Transfer</h3>
                        <div style="font-size:11px;color:#e6f2ff;margin-top:1px;">' . directbank_html($businessName) . '</div>
                    </div>
                </div>

                <button type="button"
                    onclick="document.getElementById(\'' . directbank_html($modalId) . '\').style.display=\'none\'"
                    style="background:none;border:none;color:#ffffff;font-size:24px;line-height:1;cursor:pointer;padding:0 4px;">
                    &times;
                </button>
            </div>

            <div style="padding:12px;color:#333333;font-size:13px;line-height:1.35;">

                <div style="background:#f1f7fc;border:1px solid #c9deee;border-radius:9px;padding:9px;margin-bottom:9px;">
                    <div style="font-size:12px;color:#666666;margin-bottom:6px;text-align:center;">Transfer payment to this account</div>

                    <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
                        <tr>
                            <td style="padding:4px 0;color:#555555;width:38%;font-weight:700;">Bank Name</td>
                            <td style="padding:4px 0;color:#111111;">' . directbank_html($bankName) . '</td>
                        </tr>

                        <tr>
                            <td style="padding:4px 0;color:#555555;font-weight:700;">Account Name</td>
                            <td style="padding:4px 0;color:#111111;">' . directbank_html($accountName) . '</td>
                        </tr>

                        <tr>
                            <td style="padding:4px 0;color:#555555;font-weight:700;">Account No.</td>
                            <td style="padding:4px 0;color:#111111;">
                                <strong id="bank_account_' . $safeInvoiceId . '" style="font-size:15px;color:#0b4f8a;">' . directbank_html($accountNumber) . '</strong>

                                <button type="button"
                                    onclick="
                                        var txt = document.getElementById(\'bank_account_' . $safeInvoiceId . '\').innerText;
                                        if (navigator.clipboard) { navigator.clipboard.writeText(txt); }
                                        this.innerText=\'Copied\';
                                        var btn=this;
                                        setTimeout(function(){btn.innerText=\'Copy\';},1500);
                                    "
                                    style="margin-left:5px;background:#ffffff;border:1px solid #0b4f8a;color:#0b4f8a;border-radius:5px;padding:2px 6px;font-size:11px;cursor:pointer;">
                                    Copy
                                </button>
                            </td>
                        </tr>

                        ' . $branchRow . '
                        ' . $routingRow . '
                    </table>
                </div>

                <div style="display:flex;gap:7px;margin-bottom:9px;">
                    <div style="flex:1;background:#f9f9f9;border:1px solid #eeeeee;border-radius:7px;padding:7px;text-align:center;">
                        <div style="font-size:10px;color:#777777;">Invoice</div>
                        <strong>' . directbank_html($invoiceId) . '</strong>
                    </div>

                    <div style="flex:1;background:#f9f9f9;border:1px solid #eeeeee;border-radius:7px;padding:7px;text-align:center;">
                        <div style="font-size:10px;color:#777777;">Amount</div>
                        <strong>' . directbank_html($amount) . ' ' . directbank_html($currency) . '</strong>
                    </div>
                </div>

                <div style="background:#f7f7f7;border-left:4px solid #0b4f8a;padding:7px 9px;border-radius:7px;font-size:12px;margin-bottom:9px;">
                    Use invoice number <strong>' . directbank_html($invoiceId) . '</strong> as payment reference.
                </div>

                <form onsubmit="return directbankSubmit_' . $safeInvoiceId . '();">

                    <label style="font-weight:700;margin-bottom:3px;display:block;font-size:12.5px;">Your Name</label>
                    <input type="text" id="bank_customer_' . $safeInvoiceId . '" placeholder="Enter your full name"
                        style="width:100%;padding:7px 9px;border:1px solid #cccccc;border-radius:6px;margin-bottom:7px;font-size:13px;box-sizing:border-box;">

                    <label style="font-weight:700;margin-bottom:3px;display:block;font-size:12.5px;">Transaction Reference Number</label>
                    <input type="number" id="bank_ref_' . $safeInvoiceId . '" placeholder="Example: 123456789"
                        style="width:100%;padding:7px 9px;border:1px solid #cccccc;border-radius:6px;margin-bottom:7px;font-size:13px;box-sizing:border-box;">

                    <label style="font-weight:700;margin-bottom:3px;display:block;font-size:12.5px;">Sender Account Name / Bank Name</label>
                    <input type="text" id="bank_sender_' . $safeInvoiceId . '" placeholder="Example: Your Name, Bank Name"
                        style="width:100%;padding:7px 9px;border:1px solid #cccccc;border-radius:6px;margin-bottom:7px;font-size:13px;box-sizing:border-box;">

                    <label style="font-weight:700;margin-bottom:3px;display:block;font-size:12.5px;">Paid Amount</label>
                    <input type="text" id="bank_paid_' . $safeInvoiceId . '" value="' . directbank_html($amount) . '"
                        style="width:100%;padding:7px 9px;border:1px solid #cccccc;border-radius:6px;margin-bottom:9px;font-size:13px;box-sizing:border-box;">

                    <button type="submit"
                        style="width:100%;background:#0b4f8a;border:0;color:#ffffff;padding:9px;border-radius:6px;font-size:14px;font-weight:800;cursor:pointer;">
                        Submit Payment Details
                    </button>

                </form>

                <div style="margin-top:8px;text-align:center;">
                    <button type="button"
                        onclick="return directbankEmail_' . $safeInvoiceId . '();"
                        style="background:none;border:0;color:#0b4f8a;font-size:12.5px;font-weight:700;text-decoration:none;cursor:pointer;padding:0;">
                        Or send payment proof by email
                    </button>
                </div>

                <p style="margin:8px 0 0;font-size:11px;color:#777777;text-align:center;">
                    ' . directbank_html($verificationNote) . '
                </p>

            </div>
        </div>
    </div>

    <script>
    function directbankGetData_' . $safeInvoiceId . '() {
        var customerName = document.getElementById("bank_customer_' . $safeInvoiceId . '").value.trim();
        var ref = document.getElementById("bank_ref_' . $safeInvoiceId . '").value.trim();
        var sender = document.getElementById("bank_sender_' . $safeInvoiceId . '").value.trim();
        var paid = document.getElementById("bank_paid_' . $safeInvoiceId . '").value.trim();

        if (!customerName || customerName.length < 2) {
            alert("Please enter your name.");
            return false;
        }

        if (!ref || !/^[0-9]{3,}$/.test(ref)) {
            alert("Please enter a valid transaction reference number using numbers only.");
            return false;
        }

        if (!sender || sender.length < 3) {
            alert("Please enter sender account name or bank name.");
            return false;
        }

        if (!paid || parseFloat(paid) <= 0) {
            alert("Please enter the paid amount.");
            return false;
        }

        return {
            customerName: customerName,
            ref: ref,
            sender: sender,
            paid: paid
        };
    }

    function directbankBuildMessage_' . $safeInvoiceId . '(data) {
        var invoiceId = ' . $jsInvoiceId . ';
        var amount = ' . $jsAmount . ';
        var currency = ' . $jsCurrency . ';

        return "Hello,\\n\\n"
            + "I have completed the bank transfer payment for my invoice.\\n\\n"
            + "Name: " + data.customerName + "\\n"
            + "Invoice Number: " + invoiceId + "\\n"
            + "Invoice Amount: " + amount + " " + currency + "\\n"
            + "Paid Amount: " + data.paid + " " + currency + "\\n"
            + "Transaction Reference Number: " + data.ref + "\\n"
            + "Sender Account Name / Bank Name: " + data.sender + "\\n\\n"
            + "Please verify my payment and mark the invoice as paid.\\n\\n"
            + "Thank you.";
    }

    function directbankSubmit_' . $safeInvoiceId . '() {
        var data = directbankGetData_' . $safeInvoiceId . '();

        if (!data) {
            return false;
        }

        var ticketUrlBase = ' . $jsTicketUrlBase . ';
        var message = directbankBuildMessage_' . $safeInvoiceId . '(data);

        window.location.href = ticketUrlBase + "&message=" + encodeURIComponent(message);
        return false;
    }

    function directbankEmail_' . $safeInvoiceId . '() {
        var data = directbankGetData_' . $safeInvoiceId . '();

        if (!data) {
            return false;
        }

        var supportEmail = ' . $jsSupportEmail . ';
        var subject = ' . $jsTicketSubject . ';
        var body = directbankBuildMessage_' . $safeInvoiceId . '(data);

        window.location.href = "mailto:" + supportEmail + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);
        return false;
    }

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            var modal = document.getElementById("' . directbank_html($modalId) . '");
            if (modal) {
                modal.style.display = "none";
            }
        }
    });

    var directBankModal_' . $safeInvoiceId . ' = document.getElementById("' . directbank_html($modalId) . '");
    if (directBankModal_' . $safeInvoiceId . ') {
        directBankModal_' . $safeInvoiceId . '.addEventListener("click", function(e) {
            if (e.target === this) {
                this.style.display = "none";
            }
        });
    }
    </script>';
}
