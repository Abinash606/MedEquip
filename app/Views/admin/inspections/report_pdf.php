<?php
/**
 * PDF template for inspection reports. When generating the PDF export via
 * Inspections::reportPdf(), this view is rendered with the `$latest`,
 * `$rows` and `$groupId` variables. Feel free to customise the styling
 * below to suit your organisation's branding. Minimal inline CSS is used
 * here to ensure consistent rendering in Dompdf.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inspection Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h3, h4 { margin: 0.5em 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5em; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <!-- Header with site, logo and inspection details -->
    <table style="width:100%; margin-bottom:1em;">
        <tr>
            <td style="vertical-align:top;">
                <strong>Site:</strong> <?= esc($latest['site_name'] ?? $latest['customer_name'] ?? '') ?><br>
                <em><?= esc($latest['action_performed'] ?? $latest['inspection_type'] ?? '') ?></em>
            </td>
            <td style="text-align:center; vertical-align:top;">
                <?php if (!empty($latest['logo_path'])): ?>
                    <img src="<?= base_url('uploads/logos/' . $latest['logo_path']) ?>" style="height:60px;">
                <?php endif; ?>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <strong>Inspection #:</strong> <?= esc($groupId) ?><br>
                <em>Technician: <?= esc($latest['technician_name'] ?? '') ?></em>
            </td>
        </tr>
    </table>

    <h3>Inspection Report (Group: <?= esc($groupId) ?>)</h3>

    <?php if (!empty($latest)): ?>
    <h4>Latest Added Device</h4>
    <table>
        <thead>
            <tr>
                <th>Model</th>
                <th>Type</th>
                <th>S/N</th>
                <th>Action</th>
                <th>Asset #</th>
                <th>Dept</th>
                <th>Room</th>
                <th>Tech</th>
                <th>EST</th>
                <th>CAL</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= esc($latest['model'] ?? '') ?></td>
                <td><?= esc($latest['device_type'] ?? '') ?></td>
                <td><?= esc($latest['serial_number'] ?? 'N/A') ?></td>
                <td><?= esc($latest['action_performed'] ?? '') ?></td>
                <td><?= esc($latest['asset_tag'] ?? '') ?></td>
                <td><?= esc($latest['dept'] ?? '') ?></td>
                <td><?= esc($latest['room'] ?? '') ?></td>
                <td><?= esc($latest['technician_name'] ?? 'N/A') ?></td>
                <td><?= !empty($latest['est']) ? 'Yes' : 'No' ?></td>
                <td><?= !empty($latest['cal']) ? 'Yes' : 'No' ?></td>
                <td><?= esc($latest['notes'] ?? '') ?></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <h4>Inspection Report Overview</h4>
    <table>
        <thead>
            <tr>
                <th>Result</th>
                <th>Customer</th>
                <th>Model</th>
                <th>Type</th>
                <th>S/N</th>
                <th>Action</th>
                <th>Asset #</th>
                <th>Dept</th>
                <th>Room</th>
                <th>EST</th>
                <th>CAL</th>
                <th>Tech</th>
                <th>Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= esc($r['result'] ?? '-') ?></td>
                        <td><?= esc($r['customer_name'] ?? $r['site_name'] ?? '') ?></td>
                        <td><?= esc($r['model'] ?? '') ?></td>
                        <td><?= esc($r['device_type'] ?? '') ?></td>
                        <td><?= esc($r['serial_number'] ?? 'N/A') ?></td>
                        <td><?= esc($r['action_performed'] ?? '') ?></td>
                        <td><?= esc($r['asset_tag'] ?? '') ?></td>
                        <td><?= esc($r['dept'] ?? '') ?></td>
                        <td><?= esc($r['room'] ?? '') ?></td>
                        <td><?= !empty($r['est']) ? 'Yes' : 'No' ?></td>
                        <td><?= !empty($r['cal']) ? 'Yes' : 'No' ?></td>
                        <td><?= esc($r['technician_name'] ?? 'N/A') ?></td>
                        <td><?= esc($r['inspection_date'] ?? '') ?></td>
                        <td><?= esc($r['notes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="14">No inspection records found for this group.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>