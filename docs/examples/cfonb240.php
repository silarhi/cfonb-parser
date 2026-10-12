<?php

use Silarhi\Cfonb\Cfonb240Reader;

$reader = new Cfonb240Reader();

foreach ($reader->parse(file_get_contents('transfers.txt')) as $transfer) {
    $header = $transfer->getHeader();
    echo sprintf(
        "Transfer %s, account %s %s %s\n",
        $header->getPrevTransactionFileDate()->format('Y-m-d'),
        $header->getRecipientBankCode1(),
        $header->getRecipientCounterCode1(),
        $header->getRecipientAccountNumber1()
    );

    foreach ($transfer->getTransactions() as $transaction) {
        echo sprintf(
            "  #%d  %s  %-24s  %8.2f  %s\n",
            $transaction->getSequenceNumber(),
            $transaction->getSettlementDate()->format('Y-m-d'),
            $transaction->getRecipientName1(),
            $transaction->getTransactionAmount(),
            $transaction->getRecipientName2()
        );
    }

    $total = $transfer->getTotal();
    echo sprintf("  Total #%d: %.2f\n", $total->getSequenceNumber(), $total->getTotalAmount());
}
