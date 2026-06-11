<?php
require_once 'db.php';
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

$stmt = $conn->prepare(
    "INSERT INTO enquiries
    (child_name, parent_name, contact, standard_name, reference_source, message, ip_address)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$ip = $_SERVER['REMOTE_ADDR'] ?? '';

$stmt->bind_param(
    "sssssss",
    $childName,
    $parentName,
    $contact,
    $standardText,
    $referenceText,
    $message,
    $ip
);

$stmt->execute();

echo json_encode([
    "status" => "success",
    "message" => "Enquiry submitted successfully."
]);
exit;
?>