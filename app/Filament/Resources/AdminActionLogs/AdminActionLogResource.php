<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdminActionLogs;

use App\Filament\Resources\AdminActionLogs\Pages\ListAdminActionLogs;
use App\Filament\Resources\AdminActionLogs\Tables\AdminActionLogsTable;
use App\Filament\Resources\AdminResource;
use App\Models\AdminActionLog;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class AdminActionLogResource extends AdminResource
{
    protected static ?string $model = AdminActionLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Action Logs';

    protected static string|UnitEnum|null $navigationGroup = 'Governance';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return AdminActionLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminActionLogs::route('/'),
        ];
    }
}
