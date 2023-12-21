<?php

namespace Qhopes\SatuSehat\JsonResponse;

use Qhopes\SatuSehat\Utilitys\StrHelper;

class Condition
{
    public static function convert($response)
    {
        $data = json_decode($response->body(),true);
        $resType = $data['resourceType'];
        if ($resType == 'Condition') {
            return [
                'status' => true,
                'data' => [
                    'ihs_number' => $data['id'],
                    'ihs_number_patient' => StrHelper::getIhsNumber($data['subject']['reference']),
                    'name_patient' => $data['subject']['display'] ?? '',
                    'ihs_number_encounter' => StrHelper::getIhsNumber($data['encounter']['reference']),
                    'name_encounter' => $data['encounter']['display'] ?? ''
                ]
            ];
        }
        return Error::checkOperationOutcome($resType,$data);
    }

    private static function getDiagnosis($res)
    {
        $data = [];
        $diagnosis = $res['code']['coding'];
        foreach ($diagnosis as $item) {
            $dt['code'] = $item['code'];
            $dt['name'] = $item['display'];
            $data[] = $dt;
        }
        return $data;
    }

    public static function history($response)
    {
        $history = [];
        $data = json_decode($response->body(),true);
        $entry = $data['entry'] ?? false;
        if ($entry) {
            foreach ($entry as $item) {
                $res = $item['resource'];
                $resType = $res['resourceType'];
                if ($resType == 'Condition') {
                    $dt['consent'] = 'OPTIN';
                    $dt['ihs_number'] = $res['id'];
                    $dt['ihs_number_patient'] = StrHelper::getIhsNumber($res['subject']['reference']);
                    $dt['name_patient'] = $res['subject']['display'] ?? '';
                    $dt['ihs_number_encounter'] = StrHelper::getIhsNumber($res['encounter']['reference']);
                    $dt['name_encounter'] = $res['encounter']['display'] ?? '';
                    $dt['diagnosis'] = self::getDiagnosis($res);
                } else {
                    $dt['consent'] = 'OPTOUT';
                    $dt['message'] = 'The operation did not return any information due to consent or privacy rules.';
                }
                $history[] = $dt;
            }
        }
        return $history;
    }
}