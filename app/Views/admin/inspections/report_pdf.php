<?php
/**
 * Inspection Report — dual mode:
 *   ?inline=1  → dark-themed inline tab view (matches app dark UI)
 *   default    → clean white PDF/print/modal view
 */
$h = fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');

$isInline  = !empty($_GET['inline']);

$siteName = $h($latest['site_name']        ?? $latest['customer_name'] ?? '—');
$techName = $h($latest['technician_name']  ?? 'N/A');
$action   = $h($latest['action_performed'] ?? $latest['inspection_type'] ?? '');
$logoHtml = '';
if (!empty($latest['logo_path'])) {
    $logoHtml = '<img src="' . base_url('uploads/logos/' . $latest['logo_path']) . '" style="height:55px;" alt="Logo">';
}

$failed = $repair = $passed = [];
foreach ($rows as $r) {
    $st = strtolower(trim((string)($r['result'] ?? $r['status'] ?? '')));
    if ($st === 'fail')       $failed[]  = $r;
    elseif ($st === 'repair') $repair[]  = $r;
    else                      $passed[]  = $r;
}
$needsAttn = array_merge($failed, $repair);

// ── Styles (switch between dark inline and white PDF) ───────────────────────
if ($isInline) {
    $bg        = '#0E1630';
    $bodyColor = '#e2e8f0';
    $headerBdr = 'rgba(255,255,255,.1)';
    $siteTtl   = '#f1f5f9';
    $subColor  = '#94a3b8';
    $thBg      = 'rgba(255,255,255,.05)';
    $thColor   = '#94a3b8';
    $thBdr     = 'rgba(255,255,255,.08)';
    $tdBdr     = 'rgba(255,255,255,.06)';
    $tdColor   = '#e2e8f0';
    $attnBg    = 'rgba(220,38,38,.12)';
    $attnBdr   = '#dc2626';
} else {
    $bg        = '#fff';
    $bodyColor = '#1e293b';
    $headerBdr = '#e2e8f0';
    $siteTtl   = '#0f172a';
    $subColor  = '#64748b';
    $thBg      = '#f8fafc';
    $thColor   = '#475569';
    $thBdr     = '#e2e8f0';
    $tdBdr     = '#f1f5f9';
    $tdColor   = '#1e293b';
    $attnBg    = '#fef2f2';
    $attnBdr   = '#dc2626';
}

$th = "padding:9px 12px;text-align:left;font-size:10px;font-weight:600;color:{$thColor};text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid {$thBdr};background:{$thBg};white-space:nowrap;";
$td = "padding:9px 12px;border-bottom:1px solid {$tdBdr};font-size:12px;vertical-align:middle;color:{$tdColor};";

$buildRows = function(array $items, bool $isAttn) use ($h, $td, $attnBg, $attnBdr, $isInline): string {
    if (empty($items)) {
        $msg = $isAttn ? 'No failed or repair items — all devices passed!' : 'No passed items yet.';
        return '<tr><td colspan="11" style="text-align:center;color:#94a3b8;padding:14px;font-style:italic;">' . $msg . '</td></tr>';
    }
    $out = '';
    foreach ($items as $r) {
        $res   = strtolower(trim((string)($r['result'] ?? $r['status'] ?? '')));
        $badge = $res === 'pass'
            ? '<span style="color:#16a34a;font-weight:600;">&#10003; Pass</span>'
            : ($res === 'fail'
                ? '<span style="color:#dc2626;font-weight:600;">&#10007; Fail</span>'
                : '<span style="color:#d97706;font-weight:600;">&#9881; Repair</span>');
        $date = !empty($r['inspection_date']) ? substr($r['inspection_date'], 0, 16) : '—';
        $out .= '<tr>'
            . '<td style="' . $td . '">' . $badge . '</td>'
            . '<td style="' . $td . '">' . $h($r['model'] ?? '—') . '</td>'
            . '<td style="' . $td . '">' . $h($r['device_type'] ?? '—') . '</td>'
            . '<td style="' . $td . '">' . $h($r['serial_number'] ?? 'N/A') . '</td>'
            . '<td style="' . $td . '">' . $h($r['action_performed'] ?? '—') . '</td>'
            . '<td style="' . $td . '">' . $h($r['asset_tag'] ?? '—') . '</td>'
            . '<td style="' . $td . '">' . $h($r['dept'] ?? $r['department'] ?? '—') . '</td>'
            . '<td style="' . $td . '">' . $h($r['room'] ?? $r['location'] ?? '—') . '</td>'
            . '<td style="' . $td . '">' . $h($r['technician_name'] ?? 'N/A') . '</td>'
            . '<td style="' . $td . '">' . $date . '</td>'
            . '<td style="' . $td . 'max-width:160px;">' . $h($r['notes'] ?? '') . '</td>'
            . '</tr>';
        if ($isAttn && $res === 'fail') {
            $out .= '<tr><td colspan="11" style="background:' . $attnBg . ';color:#ef4444;font-weight:600;font-size:11px;padding:5px 12px;border-left:3px solid ' . $attnBdr . ';">[!] Attention Required — Equipment Failure</td></tr>';
        }
    }
    return $out;
};

$colHeaders = '<th style="' . $th . '">Result</th><th style="' . $th . '">Model</th><th style="' . $th . '">Type</th><th style="' . $th . '">S/N</th><th style="' . $th . '">Action</th><th style="' . $th . '">Asset #</th><th style="' . $th . '">Dept</th><th style="' . $th . '">Room</th><th style="' . $th . '">Tech</th><th style="' . $th . '">Date</th><th style="' . $th . '">Notes</th>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inspection Report — <?= $h($groupId) ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: <?= $bodyColor ?>; margin: 0; padding: 24px; background: <?= $bg ?>; }
  @media print { body { background: #fff !important; color: #1e293b !important; padding: 12px; } }
</style>
</head>
<body>

<!-- Header -->
<div style="display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:18px;margin-bottom:20px;border-bottom:2px solid <?= $headerBdr ?>;">
  <div>
    <h2 style="font-size:20px;font-weight:700;color:<?= $siteTtl ?>;margin:0 0 4px;">Site: <?= $siteName ?></h2>
    <p style="color:<?= $subColor ?>;font-size:13px;font-style:italic;margin:0;"><?= $action ?></p>
  </div>
  <div style="text-align:center;"><?= $logoHtml ?></div>
  <div style="text-align:right;">
    <p style="font-size:12px;color:<?= $subColor ?>;margin:0 0 3px;"><strong>Inspection #:</strong> <?= $h($groupId) ?></p>
    <p style="font-size:12px;color:<?= $subColor ?>;margin:0 0 3px;"><strong>Technician:</strong> <?= $techName ?></p>
    <p style="font-size:12px;color:<?= $subColor ?>;margin:0;"><strong>Generated:</strong> <?= date('M d, Y g:i A') ?></p>
  </div>
</div>

<!-- Needs Attention -->
<div style="display:flex;align-items:center;gap:10px;margin:20px 0 5px;">
  <span style="font-size:18px;">&#9888;</span>
  <h3 style="font-size:16px;font-weight:700;color:#dc2626;margin:0;">Needs Attention</h3>
  <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#dc2626;color:#fff;font-size:11px;font-weight:700;"><?= count($needsAttn) ?></span>
</div>
<p style="font-size:12px;color:<?= $subColor ?>;margin:0 0 8px;">Devices that failed or require repair.</p>
<div style="overflow-x:auto;">
  <table style="width:100%;border-collapse:collapse;">
    <thead><tr><?= $colHeaders ?></tr></thead>
    <tbody><?= $buildRows($needsAttn, true) ?></tbody>
  </table>
</div>

<!-- Passed -->
<div style="display:flex;align-items:center;gap:10px;margin:28px 0 5px;">
  <span style="font-size:18px;">&#10003;</span>
  <h3 style="font-size:16px;font-weight:700;color:#16a34a;margin:0;">Passed</h3>
  <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#16a34a;color:#fff;font-size:11px;font-weight:700;"><?= count($passed) ?></span>
</div>
<p style="font-size:12px;color:<?= $subColor ?>;margin:0 0 8px;">Devices that passed inspection.</p>
<div style="overflow-x:auto;">
  <table style="width:100%;border-collapse:collapse;">
    <thead><tr><?= $colHeaders ?></tr></thead>
    <tbody><?= $buildRows($passed, false) ?></tbody>
  </table>
</div>

</body>
</html>
