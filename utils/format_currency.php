

<?php
function formatCurrency($amount)
{
    $formattedAmount = number_format($amount, 2);
    $formattedAmount = "GH₵$formattedAmount";
    return $formattedAmount;
}
?>