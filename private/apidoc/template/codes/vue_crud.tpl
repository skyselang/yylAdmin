<template>
    <div>
        <crud-table
            ref="crudTable"
            :form="form"
            :proxy-config="proxyConfig"
            :modal="modal"
            :table="table"
        >
        </crud-table>
    </div>
</template>

<script>
    import crudApi from "@/api/xxx";
    export default {
        data() {
            return {
                proxyConfig: {
                    add: {
                        submit: crudApi.{$data[2].name},
                    },
                    edit: {
                        query: (values) => {
                            return new Promise((resolve) => {
                                crudApi.{$data[1].name}({
                                        id: values.id,
                                    })
                                    .then((res) => {
                                        const data = res.data;
                                        resolve(data);
                                    });
                            });
                        },
                        submit: crudApi.{$data[3].name},
                    },
                    del: {
                        submit: crudApi.{$data[4].name},
                    },
                },
                form: {
                    props: {
                        colspan: 2,
                        items: [
                            {
                                field: "id",
                                itemRender: {
                                    name: "hidden",
                                },
                            },
                            {foreach $data[2].param as $k=>$item}
                            {
                                title: "{$item.desc}",
                                field: "{$item.name}",
                                {if '{$item.require}'=='1'}
                                option: {
                                    rules: [
                                        { required: true, message: "请输入{$item.name}" },
                                    ],
                                },
                                {/if}
                            },
                            {/foreach}
                        ],
                    },
                },
                table: {
                    props: {
                        columns: [
                            {
                                type: "seq",
                                title: "序号",
                                width: 50,
                                fixed: "left",
                            },
                            {foreach $data[0].returned[4].children as $k=>$item}
                            {
                                title: "{$item.desc}",
                                field: "{$item.name}",
                                width: 150,
                            },
                            {/foreach}

                        ],
                        headToolbar: {
                            search: {
                                layout: "inline",
                                titleWidth: "auto",
                                items: [
                                    {foreach $data[0].query as $k=>$item}
                                    {
                                        title: "{$item.desc}",
                                        field: "{$item.name}",
                                    },
                                    {/foreach}
                                ],
                            },
                        },
                        proxyConfig: {
                            sort: true,
                            ajax: {
                                query: crudApi.{$data[0].name},
                            },
                        },
                    },
                },
            };
        },
        methods: {
        },
    };
</script>
