<?php
header('Content-Type: application/json');

// Only POST request allowed
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
    exit;
}

// Function to sanitize input
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Get and sanitize form data
$childName  = isset($_POST['childName']) ? clean($_POST['childName']) : '';
$parentName = isset($_POST['parentName']) ? clean($_POST['parentName']) : '';
$contact    = isset($_POST['contact']) ? clean($_POST['contact']) : '';
$standard   = isset($_POST['standard']) ? clean($_POST['standard']) : '';
$reference  = isset($_POST['reference']) ? clean($_POST['reference']) : '';
$message    = isset($_POST['message']) ? clean($_POST['message']) : '';

// Basic validation
if (empty($childName) || empty($parentName) || empty($contact) || empty($standard) || empty($reference)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill all required fields."
    ]);
    exit;
}

// Validate Indian mobile number
if (!preg_match('/^[6-9][0-9]{9}$/', $contact)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid contact number."
    ]);
    exit;
}

// Standard label mapping
$standardLabels = [
    "playgroup" => "Play Group (Age 1.5–2.5)",
    "nursery"   => "Nursery (Age 2.5–3.5)",
    "lkg"       => "Jr. KG / LKG (Age 3.5–4.5)",
    "ukg"       => "Sr. KG / UKG (Age 4.5–5.5)"
];

$referenceLabels = [
    "google"  => "Google Search",
    "self"    => "Self / Known",
    "walking" => "Walking By",
    "friend"  => "Friend / Family",
    "social"  => "Social Media"
];

$standardText  = $standardLabels[$standard] ?? $standard;
$referenceText = $referenceLabels[$reference] ?? $reference;

// Folder and file path
$dataDir = __DIR__ . "/data";
$filePath = $dataDir . "/enquiries.csv";

// Create data directory if not exists
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

// Check if file already exists
$fileExists = file_exists($filePath);

// Open file in append mode
$file = fopen($filePath, 'a');

if (!$file) {
    echo json_encode([
        "status" => "error",
        "message" => "Unable to save data."
    ]);
    exit;
}

// Add BOM for proper Excel UTF-8 support
if (!$fileExists) {
    fwrite($file, "\xEF\xBB\xBF");
    fputcsv($file, [
        "Date Time",
        "Child Name",
        "Parent Name",
        "Contact Number",
        "Standard",
        "Reference",
        "Message",
        "IP Address"
    ]);
}

// Save row
fputcsv($file, [
    date("Y-m-d H:i:s"),
    $childName,
    $parentName,
    $contact,
    $standardText,
    $referenceText,
    $message,
    $_SERVER['REMOTE_ADDR'] ?? ''
]);

fclose($file);

echo json_encode([
    "status" => "success",
    "message" => "Enquiry submitted successfully."
]);
exit;
?>