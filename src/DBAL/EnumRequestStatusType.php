<?php

namespace IHelpShopping\DBAL;

class EnumRequestStatusType extends EnumType
{
    protected $name = 'enumrequeststatus';
    protected $values = ['draft', 'pending', 'approved', 'cancelled', 'declined'];
}
