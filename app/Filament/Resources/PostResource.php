<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Create a new Post')->tabs([
                    Tab::make('Post')
                        ->icon('heroicon-o-inbox')
                        ->badge('New')
                        ->schema([
                            TextInput::make('title')->minLength(3)->maxLength(10)->required(),
                            TextInput::make('slug')->required()->unique(ignoreRecord: true),
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->required(),
                            ColorPicker::make('color')->required(),
                        ]),
                    Tab::make('Content')->schema([
                        MarkdownEditor::make('content')->required()->columnSpanFull(),
                    ]),
                    Tab::make('Image')->schema([
                        FileUpload::make('thumbnail')
                            ->disk('public')
                            ->directory('thumbnails'),
                    ]),
                    Tab::make('Meta')->schema([
                        TagsInput::make('tags')->required()->columnSpanFull(),
                        Checkbox::make('published'),
                    ]),
                ])->columnSpanFull()->columns(2)->activeTab(1)->persistTabInQueryString(),

                // Section::make('Create Post')
                //     ->description('Create a new post here')
                //     ->schema([])->columnSpan(2)->columns(2),

                // Group::make()->schema([
                //     Section::make('Image')
                //         ->collapsible()
                //         ->schema([
                //             FileUpload::make('thumbnail')
                //                 ->disk('public')
                //                 ->directory('thumbnails'),
                //         ])->columnSpan(1),
                //     Section::make('Meta')->schema([
                //         TagsInput::make('tags')->required(),
                //         Checkbox::make('published'),
                //     ]),
                //     // Section::make('Authors')->schema([
                //     //     CheckboxList::make('authors',)
                //     //         ->label('Co authors')
                //     //         // ->multiple()
                //     //         ->relationship('authors', 'name')
                //     //     // ->preload()
                //     // ]),
                // ])->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Published on')
                    ->date('d M Y')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                ImageColumn::make('thumbnail')
                    ->toggleable(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('slug')
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                ColorColumn::make('color')
                    ->toggleable(),
                TextColumn::make('tags')
                    ->toggleable(),
                CheckboxColumn::make('published')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
