<?php

namespace Qhopes\SatuSehat\JsonData;

use Qhopes\SatuSehat\Utilitys\DateTimeFormat;

class Observation
{
    public static function formCreateData($encounter,$code,$name,$value,$unit)
    {
        return [
            "resourceType"=> "Observation",
            "status"=> "final",
            "category"=> [
                [
                    "coding"=> [
                        [
                            "system"=> "http://terminology.hl7.org/CodeSystem/observation-category",
                            "code"=> "vital-signs",
                            "display"=> "Vital Signs"
                        ]
                    ]
                ]
            ],
            "code"=> [
                "coding"=> [
                    [
                        "system"=> "http://loinc.org",
                        "code"=> $code,
                        "display"=> $name
                    ]
                ]
            ],
            "subject"=> [
                "reference"=> "Patient/".$encounter->patient->ihs_number,
                "display"=> $encounter->patient->name
            ],
            "performer"=> [
                [
                    "reference"=> "Practitioner/".$encounter->practitioner->ihs_number,
                    "display"=> $encounter->practitioner->name
                ]
            ],
            "encounter"=> [
                "reference"=> "Encounter/".$encounter->ihs_number
            ],
            "effectiveDateTime"=> DateTimeFormat::dateNow(),
            "issued"=> DateTimeFormat::now(),
            "valueQuantity"=> [
                "system"=> "http://unitsofmeasure.org",
                "value"=> $value,
                "unit"=> $unit,
                "code"=> $unit
            ]
        ];
    }

    public static function formUpdateData($ihsNumber,$encounter,$code,$name,$value,$unit)
    {
        return [
            "resourceType"=> "Observation",
            "id"=> $ihsNumber,
            "status"=> "final",
            "category"=> [
                [
                    "coding"=> [
                        [
                            "system"=> "http://terminology.hl7.org/CodeSystem/observation-category",
                            "code"=> "vital-signs",
                            "display"=> "Vital Signs"
                        ]
                    ]
                ]
            ],
            "code"=> [
                "coding"=> [
                    [
                        "system"=> "http://loinc.org",
                        "code"=> $code,
                        "display"=> $name
                    ]
                ]
            ],
            "subject"=> [
                "reference"=> "Patient/".$encounter->patient->ihs_number
            ],
            "performer"=> [
                [
                    "reference"=> "Practitioner/".$encounter->practitioner->ihs_number
                ]
            ],
            "encounter"=> [
                "reference"=> "Encounter/".$encounter->ihs_number
            ],
            "effectiveDateTime"=> $encounter->observation->effective,
            "issued"=> $encounter->observation->issued,
            "valueQuantity"=> [
                "system"=> "http://unitsofmeasure.org",
                "value"=> $value,
                "unit"=> $unit,
                "code"=> $unit
            ]
        ];
    }
}