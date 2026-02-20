<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentSettingModel extends Model
{
  protected $table = 'equipment_settings';
  protected $primaryKey = 'id';
  protected $allowedFields = ['description', 'est', 'cal'];
  protected $useTimestamps = true;
}
