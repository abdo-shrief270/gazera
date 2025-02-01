<?php

namespace App\Filament\Resources\AppResource\Pages;

use App\Filament\Resources\AppResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected function getActions(): array
    {
        return [
            Action::make('dashboard')
                ->color('success')
                ->label('make an invoice')
                ->icon('heroicon-o-user')
        ];
    }
    public function getColumns(): int | string | array
    {
        return 1;
    }
}
