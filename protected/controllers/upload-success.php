<h3>Upload Member Berhasil</h3>

<p>Data member berhasil di-upload.</p>

<table>
    <tr>
        <td><b>Policy No</b></td>
        <td>: <?= $policyNo ?></td>
    </tr>
    <tr>
        <td><b>Batch No</b></td>
        <td>: <?= $batchNo ?></td>
    </tr>
    <tr>
        <td><b>Total Member</b></td>
        <td>: <?= $totalMember ?></td>
    </tr>
    <tr>
        <td><b>Total UP</b></td>
        <td>: <?= number_format($totalUp, 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td><b>Total Nett Premium</b></td>
        <td>: <?= number_format($totalNettPremium, 0, ',', '.') ?></td>
    </tr>
</table>

<p>Terima kasih.</p>