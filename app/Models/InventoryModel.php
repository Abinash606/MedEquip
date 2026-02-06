<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'part_number',
        'part_description',
        'bin',
        'row_aisle',
        'shelf',
        'qty',
        'total_value',
        'image'
    ];

    protected $validationRules = [
        'part_number'        => 'required|max_length[100]',
        'part_description'   => 'required|max_length[255]',
        'bin'                => 'required|max_length[50]',
        'row_aisle'          => 'required|max_length[100]',
        'shelf'              => 'required|max_length[100]',
        'qty'                => 'required|integer',
        'total_value'        => 'required|decimal',
    ];

    protected $validationMessages = [
        'part_number' => [
            'required' => 'Part number is required',
            'max_length' => 'Part number cannot exceed 100 characters'
        ],
        'part_description' => [
            'required' => 'Part description is required',
            'max_length' => 'Part description cannot exceed 255 characters'
        ],
        'bin' => [
            'required' => 'Bin is required',
            'max_length' => 'Bin cannot exceed 50 characters'
        ],
        'row_aisle' => [
            'required' => 'Row/Aisle is required',
            'max_length' => 'Row/Aisle cannot exceed 100 characters'
        ],
        'shelf' => [
            'required' => 'Shelf is required',
            'max_length' => 'Shelf cannot exceed 100 characters'
        ],
        'qty' => [
            'required' => 'Quantity is required',
            'integer' => 'Quantity must be a number'
        ],
        'total_value' => [
            'required' => 'Total value is required',
            'decimal' => 'Total value must be a decimal number'
        ],
    ];
}
