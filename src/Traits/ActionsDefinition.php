<?php
/*
 * Project: edistribucion_api
 * Author: acardiel
 * Date: 29/3/22 23:57
 * Version: 0.1
 *
 * Please note: this package is released for use "AS IS" without any warranties of any kind,
 * including, but not limited to their installation, use, or performance. We disclaim any and
 * all warranties, either express or implied, including but not limited to any warranty of
 * noninfringement, merchantability, and/ or fitness for a particular purpose. We do not
 * warrant that the technology will meet your requirements, that the operation thereof
 * will be uninterrupted or error-free, or that any errors will be corrected.
 *
 * Any use of these scripts and tools is at your own risk. There is no guarantee that they
 * have been through thorough testing in a comparable environment and we are not
 * responsible for any damage or data loss incurred with their use.
 *
 * You are responsible for reviewing and testing any scripts you run thoroughly before use
 *  in any non-testing environment.
 */

namespace Edistribucion\Traits;

use DateTime;
use DateTimeImmutable;
use Edistribucion\Actions as Actions;
use Edistribucion\Models\CUPS;
use Exception;

trait ActionsDefinition
{

    /**
     * @throws Exception
     */
    public function get_login_info(): string|array
    {
        return $this->run_action_command(
            new Actions\GetLoginInfo()
        );
    }

    /**
     * @throws Exception
     */
    public function get_cups(): string|array
    {
        return $this->run_action_command(
            new Actions\GetCups(["visSelected" => $this->identities['account_id']])
        );
    }

    /**
     * @throws Exception
     */
    public function get_cups_info(string $cupsId): string|array
    {
        return $this->run_action_command(
            new Actions\GetCupsInfo([
                "cupsId" => $cupsId,
                "visSelected" => $this->identities['account_id']
            ])
        );
    }

    /**
     * @throws Exception
     */
    public function get_meter(string $cupsId): string|array
    {
        return $this->run_action_command(
            new Actions\GetMeter(["cupsId" => $cupsId])
        );
    }

    /**
     * @throws Exception
     */
    public function get_all_cups(): string|array
    {
        return $this->run_action_command(
            new Actions\GetAllCups([
                "visSelected" => $this->identities['account_id']
            ])
        );
    }

    /**
     * @throws Exception
     */
    public function get_cups_detail(string $cupsId): string|array
    {
        return $this->run_action_command(
            new Actions\GetCupsDetail([
                "visSelected" => $this->identities['account_id'],
                "cupsId" => $cupsId
            ])
        );
    }

    /**
     * @throws Exception
     */
    public function get_cups_status(string $cupsId): string|array
    {
        return $this->run_action_command(
            new Actions\GetCupsStatus([
                "cupsId" => $cupsId
            ])
        );
    }

    /**
     * @throws Exception
     */
    public function get_atr_detail(string $atrId): string|array
    {
        return $this->run_action_command(
            new Actions\GetAtrDetail([
                "atrId" => $atrId
            ])
        );
    }

    /**
     * @throws Exception
     */
    public function get_solicitud_atr_detail(string $solId): string|array
    {
        return $this->run_action_command(
            new Actions\GetSolicitudAtrDetail([
                "solId" => $solId
            ])
        );
    }

    /**
     * La orden de  reconexión ha sido enviada con éxito a tu contador.
     * En caso de que habiendo activado el ICP sigas sin tener suministro,
     * llama a Averías 900 850 840
     *
     * @throws Exception
     */
    public function reconnect_ICP(string $cupsId): string|array
    {
        $r = $this->run_action_command(
            new Actions\ReconnectICPDetail([
                "cupsId" => $cupsId
            ])
        );

        return $this->run_action_command(
            new Actions\ReconnectICPModal([
                "cupsId" => $cupsId
            ])
        );
    }

    /**
     * @return array<CUPS>
     * @throws Exception
     */
    public function get_list_cups(): array
    {
        $response = $this->run_action_command(
            new Actions\GetListCups([
                "sIdentificador" => $this->identities['account_id']
            ])
        );

        $cupsList = [];
        foreach ($response['data']['lstCups'] as $cont) {
            if (in_array($cont['Id'], $response['data']['lstIds'])) {
                $cupsList[] = new CUPS(
                    cups: $cont['CUPs__r']['Name'],
                    cupsId: $cont['CUPs__r']['Id'],
                    id: $cont['Id'],
                    active: !isset($cont['Version_end_date__c']),
                    power: $cont['Requested_power_1__c'],
                    rate: $cont['rate']
                );
            }
        }

        return $cupsList;
    }

    /**
     * @param string $contId
     *
     * @return string|array
     * @throws Exception
     */
    public function get_list_cycles(string $contId): string|array
    {
        return $this->run_action_command(
            new Actions\GetListCycles([
                "contId" => $contId
            ])
        );
    }

    /**
     * @param string $cups
     * @param string $cycleLabel
     * @param string $cycleValue
     *
     * @return string|array
     * @throws Exception
     */
    public function get_meas(string $cups, string $cycleLabel, string $cycleValue): string|array
    {
        return $this->run_action_command(
            new Actions\GetMeas([
                "cupsId" => $cups,
                "dateRange" => $cycleLabel,
                "cfactura" => $cycleValue
            ])
        );
    }

    /**
     * @param string $contId
     *
     * @return string|array
     * @throws Exception
     */
    public function get_measure(string $contId): string|array
    {
        $yesterday = new DateTime('yesterday');
        return $this->run_action_command(
            new Actions\GetMeasure([
                "contId" => $contId,
                "type" => 1,
                "startDate" => $yesterday->format("Y-m-d")
            ])
        );
    }

    /**
     * @param string $contId
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return string|array
     * @throws Exception
     */
    public function get_maximeter(string $contId, DateTime $startDate, DateTime $endDate): string|array
    {
        return $this->run_action_command(
            new Actions\GetMaximeter([
                "mapParams" => [
                    "startDate" => $startDate->format("Y-m-d"),
                    "endDate" => $endDate->format("Y-m-d"),
                    "id" => "******",
                    "sIdentificador" => "*****"
                ]
            ])
        );
    }

    /**
     * @param CUPS $cups
     * @param DateTimeImmutable|string $startDate
     * @param DateTimeImmutable|string $endDate
     *
     * @return array|string
     * @throws Exception
     */
    public function get_meas_interval(
        CUPS $cups,
        DateTimeImmutable|string $startDate,
        DateTimeImmutable|string $endDate
    ): array|string {
        if (is_string($startDate)) {
            $startDate = DateTimeImmutable::createFromFormat("d/m/Y", $startDate);
        }

        if (is_string($endDate)) {
            $endDate = DateTimeImmutable::createFromFormat("d/m/Y", $endDate);
        }

        return $this->run_action_command(
            new Actions\GetMeasInterval([
                "startDate" => $startDate->format("Y-m-d"),
                "endDate" => $endDate->format("Y-m-d"),
                "type" => 4,
                "contId" => $cups->getId()
            ])
        );
    }
}
