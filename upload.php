<?php
$data = [];

if ($_POST['submit']) {
    $files = $_FILES['files'];

    echo "
    <table>
        <thead>
            <th> Invoice ID </th>
            <th> Date </th>
            <th> Total (Excl. VAT) </th>
            <th> VAT Percentage </th>
            <th> Total (Incl. VAT) </th>
        </thead>
        <tbody>
    ";
    
    for($i = 0; $i < sizeof($files['name']); $i++) {
        if($files['type'][$i] == "text/xml") {
            $file = $files['tmp_name'][$i];

            $xml = simplexml_load_file($file, NULL, 0, 'cbc');
            $namespaces = $xml->getNamespaces(true);
            

            foreach ($namespaces as $prefix => $ns) {
                $xml->registerXPathNamespace($prefix, $ns);
            }
            $xml->registerXPathNamespace('def', $namespaces['']);   // Register default namespace with a custom prefix

            array_push($data, [
                "ID" => $xml->xpath('/def:Invoice/cbc:ID')[0],
                "date" => $xml->xpath('//cbc:IssueDate')[0],
                "total" => $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxExclusiveAmount')[0],
                "vat_percent" => $xml->xpath('//cbc:Percent')[0],
                "total_vat" => $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0]
            ]);
        }
    }

    foreach ($data as $row) {
        echo "
        <tr>
            <td>".$row["ID"]."<td>
            <td>".$row["date"]."<td>
            <td>".$row["total"]."<td>
            <td>".$row["vat_percent"]."<td>
            <td>".$row["total_vat"]."<td>
        </tr>
        ";
    }

    echo "</tbody></table>";

} else {
    echo "<h1> No input </h1>";
}