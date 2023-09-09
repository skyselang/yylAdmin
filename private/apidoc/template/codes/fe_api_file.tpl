{$form.http_import}

export const URLS = {
{foreach $data.children as $k=>$item}
    "{$item.name}": "{$item.url}",
{/foreach}
};

export default class {$lcfirst(data.controller)}Api {
{foreach $data.children as $k=>$item}
    {if '{$form.show_desc}'=='true'}// {$item.title}{/if}
    static {$item.name}(data) {
        return sendRequest(URLS.{$item.name}, data, "{$item.method}");
    }
{/foreach}
}
