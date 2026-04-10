<?php
declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\ChatWidget;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class InstallationPage extends Page
{
    protected string $view = 'filament.pages.installation-page';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Instalación';
    protected static string|\UnitEnum|null $navigationGroup = 'Widget';
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-code-bracket';
    }

    public function getTitle(): string|Htmlable { return 'Instalación del Widget'; }

    public string $activePlatform = 'any';
    public ?int   $selectedWidget = null;

    public function mount(): void
    {
        $first = $this->orgWidgets()->first();
        if ($first) {
            $this->selectedWidget = $first->id;
        }
    }

    public function orgWidgets(): Collection
    {
        $orgId = auth()->user()?->organization_id;
        return ChatWidget::when($orgId, fn ($q) => $q->where('organization_id', $orgId))
            ->orderBy('name')
            ->get(['id', 'name', 'token', 'is_active']);
    }

    public function getSelectedWidgetModel(): ?ChatWidget
    {
        return $this->selectedWidget ? ChatWidget::find($this->selectedWidget) : null;
    }

    public function setPlatform(string $platform): void
    {
        $allowed = ['any', 'wordpress', 'shopify', 'wix', 'laravel', 'react', 'squarespace'];
        if (in_array($platform, $allowed, true)) {
            $this->activePlatform = $platform;
        }
    }

    public function getEmbedCode(): string
    {
        $appUrl = rtrim(config('app.url', url('/')), '/');
        $token  = $this->getSelectedWidgetModel()?->token ?? 'TU_WIDGET_TOKEN';

        return "<script>\n  window.NexovaChatConfig = { apiUrl: \"{$appUrl}\", widgetToken: \"{$token}\" };\n</script>\n<script src=\"{$appUrl}/widget.js\" defer></script>";
    }

    public function getWooCommerceSnippet(): string
    {
        $appUrl = rtrim(config('app.url', url('/')), '/');
        $token  = $this->getSelectedWidgetModel()?->token ?? 'TU_WIDGET_TOKEN';

        return "add_action('wp_footer', function () {\n    \$customer = [];\n    if (is_user_logged_in()) {\n        \$uid   = get_current_user_id();\n        \$user  = wp_get_current_user();\n        \$hmac  = hash_hmac('sha256', \$uid . '|' . \$user->user_email, '{$token}');\n        \$customer = ['id' => \$uid, 'email' => \$user->user_email,\n                     'name' => \$user->display_name, 'token' => \$hmac];\n    }\n    echo \"<script>window.NexovaChatConfig={apiUrl:'{$appUrl}',widgetToken:'{$token}',customer:\".wp_json_encode(\$customer).\"}</s\".\"cript>\";\n    echo \"<script src='{$appUrl}/widget.js' defer></s\".\"cript>\";\n});";
    }

    public function getReactCode(): string
    {
        $appUrl = rtrim(config('app.url', url('/')), '/');
        $token  = $this->getSelectedWidgetModel()?->token ?? 'TU_WIDGET_TOKEN';

        return "import { useEffect } from 'react';\n\nexport default function NexovaChat() {\n  useEffect(() => {\n    window.NexovaChatConfig = { apiUrl: '{$appUrl}', widgetToken: '{$token}' };\n    const s = document.createElement('script');\n    s.src = '{$appUrl}/widget.js';\n    s.defer = true;\n    document.body.appendChild(s);\n    return () => document.body.removeChild(s);\n  }, []);\n  return null;\n}\n// En tu layout: <NexovaChat />";
    }
}
