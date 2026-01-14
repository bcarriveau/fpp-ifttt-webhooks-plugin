<?php
include_once "/opt/fpp/www/common.php"; // Allows use of FPP Functions
include_once "iftttw-common.php";

// Manual JSON loading (already done in common, but ensure global access)
global $pluginSettings, $settings, $pluginName;
$pluginConfigFile = $settings['configDirectory'] . "/plugin." . $pluginName . ".json";

// Migration from old INI config
$oldConfigFile = $settings['configDirectory'] . "/plugin." . $pluginName;
if (file_exists($oldConfigFile) && !file_exists($pluginConfigFile)) { // If old INI exists and JSON doesn't
    $oldSettings = parse_ini_file($oldConfigFile);
    if (isset($oldSettings['ifttt_key'])) {
        $pluginSettings['ifttt_key'] = $oldSettings['ifttt_key']; // Copy key
        file_put_contents($pluginConfigFile, json_encode($pluginSettings, JSON_PRETTY_PRINT)); // Save to new JSON
        @unlink($oldConfigFile); // Clean up old file
    }
}

$iftttKey = isset($pluginSettings['ifttt_key']) ? urldecode($pluginSettings['ifttt_key']) : '';

$isConfigured = false;
if (strlen($iftttKey) > 0) {
    $isConfigured = true;
}

// Handle AJAX POST for saving
if (isset($_POST['submit'])) {
    $pluginSettings['ifttt_key'] = urlencode($_POST['ifttt_key']);
    file_put_contents($pluginConfigFile, json_encode($pluginSettings, JSON_PRETTY_PRINT)); // Manual save as .json
    echo "Settings saved";
    exit; // Prevent full page output for nopage=1
}
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
<div class="pluginBody" style="margin-left: 1em;">
    <div class="title">
        <h1>IFTTT Webhooks Settings</h1>
        <h4></h4>
    </div>

    <p>Press F1 for setup instructions</p>

    <table cellspacing="5">
        <tr>
            <th style="text-align: left">IFTTT Webhook Key</th>
            <td>
                <input type="text" id="ifttt_key" maxlength="50" size="50" value="<?php echo htmlspecialchars($iftttKey); ?>">
                <input type="button" class="buttons" value="Save" onclick="saveKey();">
            </td>
        </tr>
    </table>

    <br />
    <br />
    <hr />

    <?php if ($isConfigured) { ?>
    <h3>Send Test Event</h3>

    <div>
        <table cellspacing="5">
            <tbody>
                <tr>
                    <th style="text-align: left">Event Name</th>
                    <td>
                        <input type="text" id="test_event_name" maxlength="50" size="50" value="lead_in">
                    </td>
                </tr>

                <tr>
                    <th style="text-align: left">Payload</th>
                    <td>
                        <select id="test_event_payload">
                            <?php
                            $payloads = getPayloadOptions();
                            foreach ($payloads as $item) {
                                $name = $item['name'];
                                echo '<option value="' . $name . '">' . $name . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>
                        <button onclick="testButton();">Send Test Event</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php } ?>

    <script type="text/javascript">
    function saveKey() {
        var key = $('#ifttt_key').val();
        $.post('plugin.php?plugin=<?php echo $pluginName; ?>&page=ifttt-webhooks.php&nopage=1', { submit: '1', ifttt_key: key }).done(function(data) {
            alert(data); // Will show "Settings saved"
            location.reload(true);
        });
    }

    function testButton() {
        ten = $('#test_event_name').val();
        tep = $('#test_event_payload').val();
        testExecute(ten, tep);
    }

    function testExecute(name, po) {
        url = '/api/command/' + encodeURIComponent('IFTTT Webhook Trigger') + '/' + encodeURIComponent(name) + '/' + encodeURIComponent(po);
        $.get(url, function(data) {
            alert('Event fired. Check execute log for details.');
        });
    }
    </script>

</body>
</html>