<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\Department;
use App\Models\Tag;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class DepartmentsPage extends Page
{
    use ScopedToOrganization;

    protected string $view = 'filament.pages.departments-page';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Departamentos y Tags';
    protected static string|\UnitEnum|null $navigationGroup = 'Agentes';
    protected static ?int $navigationSort = 25;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-tag';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── Department form ──────────────────────────────────────────────────────
    public bool   $showDeptModal  = false;
    public ?int   $editDeptId     = null;
    public string $deptName       = '';
    public string $deptColor      = '#6366f1';
    public string $deptDesc       = '';
    public bool   $deptActive     = true;

    // ── Tag form ─────────────────────────────────────────────────────────────
    public bool   $showTagModal = false;
    public ?int   $editTagId   = null;
    public string $tagName     = '';
    public string $tagColor    = '#22c55e';

    // ── Data ─────────────────────────────────────────────────────────────────
    public function getDepartmentsProperty()
    {
        return $this->scopeToOrg(Department::query())
            ->withCount('tickets')
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    public function getTagsProperty()
    {
        return $this->scopeToOrg(Tag::query())
            ->withCount('tickets')
            ->orderBy('name')
            ->get();
    }

    // ── Department actions ───────────────────────────────────────────────────
    public function openDeptModal(?int $id = null): void
    {
        $this->editDeptId = $id;
        if ($id) {
            $dept = Department::find($id);
            $this->deptName   = $dept->name;
            $this->deptColor  = $dept->color;
            $this->deptDesc   = $dept->description ?? '';
            $this->deptActive = $dept->is_active;
        } else {
            $this->deptName  = '';
            $this->deptColor = '#6366f1';
            $this->deptDesc  = '';
            $this->deptActive = true;
        }
        $this->showDeptModal = true;
    }

    public function saveDept(): void
    {
        $name = trim($this->deptName);
        if (! $name) return;

        $orgId = $this->orgId();
        $data  = [
            'organization_id' => $orgId,
            'name'            => $name,
            'color'           => $this->deptColor ?: '#6366f1',
            'description'     => trim($this->deptDesc) ?: null,
            'is_active'       => $this->deptActive,
        ];

        if ($this->editDeptId) {
            Department::where('id', $this->editDeptId)
                ->where('organization_id', $orgId)
                ->update($data);
        } else {
            Department::create($data);
        }

        $this->showDeptModal = false;
        $this->dispatch('nexova-toast', type: 'success', message: 'Departamento guardado');
    }

    public function deleteDept(int $id): void
    {
        Department::where('id', $id)
            ->where('organization_id', $this->orgId())
            ->delete();
        $this->dispatch('nexova-toast', type: 'success', message: 'Departamento eliminado');
    }

    // ── Tag actions ──────────────────────────────────────────────────────────
    public function openTagModal(?int $id = null): void
    {
        $this->editTagId = $id;
        if ($id) {
            $tag = Tag::find($id);
            $this->tagName  = $tag->name;
            $this->tagColor = $tag->color;
        } else {
            $this->tagName  = '';
            $this->tagColor = '#22c55e';
        }
        $this->showTagModal = true;
    }

    public function saveTag(): void
    {
        $name = trim($this->tagName);
        if (! $name) return;

        $orgId = $this->orgId();
        $data  = [
            'organization_id' => $orgId,
            'name'            => $name,
            'color'           => $this->tagColor ?: '#22c55e',
        ];

        if ($this->editTagId) {
            Tag::where('id', $this->editTagId)
               ->where('organization_id', $orgId)
               ->update($data);
        } else {
            Tag::create($data);
        }

        $this->showTagModal = false;
        $this->dispatch('nexova-toast', type: 'success', message: 'Tag guardado');
    }

    public function deleteTag(int $id): void
    {
        Tag::where('id', $id)
           ->where('organization_id', $this->orgId())
           ->delete();
        $this->dispatch('nexova-toast', type: 'success', message: 'Tag eliminado');
    }
}
