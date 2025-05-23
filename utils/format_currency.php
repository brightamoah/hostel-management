<!-- function to format ghana cedis currency -->

<?php
function formatCurrency($amount)
{
    // Format the amount with two decimal places
    $formattedAmount = number_format($amount, 2);

    // Add the Ghana Cedis symbol (₵)
    $formattedAmount = "GH₵$formattedAmount";

    return $formattedAmount;
}
?>