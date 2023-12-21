<?php

namespace Qhopes\SatuSehat\JsonResponse;

use Qhopes\SatuSehat\Utilitys\StrHelper;

class Observation
{
    public static function convert($response)
    {
        $data = json_decode($response->body(),true);
        $resType = $data['resourceType'];
        if ($resType == 'Observation') {
            return [
                'status' => true,
                'data' => [
                    'ihs_number' => $data['id'],
                    'issued' => $data['issued'],
                    'code' => $data['code']['coding'][0]['code'],
                    'name' => $data['code']['coding'][0]['display'],
                    'value' => $data['valueQuantity']['value'],
                    'unit' => $data['valueQuantity']['unit'],
                    'ihs_number_patient' => StrHelper::getIhsNumber($data['subject']['reference']),
                    'name_patient' => $data['subject']['display'] ?? '',
                    'ihs_number_practitioner' => StrHelper::getIhsNumber($data['performer'][0]['reference']),
                    'name_practitioner' => $data['performer'][0]['display'] ?? '',
                    'ihs_number_encounter' => StrHelper::getIhsNumber($data['encounter']['reference']),
                    'name_encounter' => $data['encounter']['display'] ?? ''
                ]
            ];
        }
        return Error::checkOperationOutcome($resType,$data);
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
                    $dt['issued'] = $res['issued'];
                    $dt['code'] = $res['code']['coding'][0]['code'];
                    $dt['name'] = $res['code']['coding'][0]['display'];
                    $dt['value'] = $res['valueQuantity']['value'];
                    $dt['unit'] = $res['valueQuantity']['unit'];
                    $dt['ihs_number_patient'] = StrHelper::getIhsNumber($res['subject']['reference']);
                    $dt['name_patient'] = $res['subject']['display'] ?? '';
                    $dt['ihs_number_practitioner'] = StrHelper::getIhsNumber($res['performer'][0]['reference']);
                    $dt['name_practitioner'] = $res['performer'][0]['display'] ?? '';
                    $dt['ihs_number_encounter'] = StrHelper::getIhsNumber($res['encounter']['reference']);
                    $dt['name_encounter'] = $res['encounter']['display'] ?? '';
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