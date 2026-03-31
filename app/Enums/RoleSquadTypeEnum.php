<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleSquadTypeEnum: string
{
    case Commander = 'commander';
    case SquadLeader = 'squad_leader';
    case Rifleman = 'rifleman';
    case Assault = 'assault';
    case AutomaticRifleman = 'automatic_rifleman';
    case Medic = 'medic';
    case Antitank = 'antitank';
    case Support = 'support';
    case MachineGunner = 'machine_gunner';
    case Engineer = 'engineer';
    case TankCommander = 'tank_commander';
    case Crewman = 'crewman';
    case Spotter = 'spotter';
    case Sniper = 'sniper';
    case ArtilleryOperator = 'artillery_operator';
    case ArtilleryGunner = 'artillery_gunner';

    public function label(): string
    {
        return match ($this) {
            self::Commander => __('hll.role_squad_type.commander'),
            self::SquadLeader => __('hll.role_squad_type.squad_leader'),
            self::Rifleman => __('hll.role_squad_type.rifleman'),
            self::Assault => __('hll.role_squad_type.assault'),
            self::AutomaticRifleman => __('hll.role_squad_type.automatic_rifleman'),
            self::Medic => __('hll.role_squad_type.medic'),
            self::Antitank => __('hll.role_squad_type.antitank'),
            self::Support => __('hll.role_squad_type.support'),
            self::MachineGunner => __('hll.role_squad_type.machine_gunner'),
            self::Engineer => __('hll.role_squad_type.engineer'),
            self::TankCommander => __('hll.role_squad_type.tank_commander'),
            self::Crewman => __('hll.role_squad_type.crewman'),
            self::Spotter => __('hll.role_squad_type.spotter'),
            self::Sniper => __('hll.role_squad_type.sniper'),
            self::ArtilleryOperator => __('hll.role_squad_type.artillery_operator'),
            self::ArtilleryGunner => __('hll.role_squad_type.artillery_gunner'),
        };
    }
}
