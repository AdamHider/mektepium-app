const replicaItemConfig = {
    label: 'Реплика',
    class: 'chat-message mt-2 p-2 row rounded w-75',
    render: (e) => {$(e).removeClass('left right').addClass($(e).find('.float-select select').val())},
    tag: 'div',
    fields: {
        image: {
            type: 'fieldGroup',
            class: 'field-group col-3',
            tag: 'div',
            nolabel: true,
            fields: {
                image: { type: 'image', class: 'chat-avatar', nolabel: true, default: '' },
            }
        },
        name: {
            type: 'fieldGroup',
            class: 'field-group col-9',
            tag: 'div',
            nolabel: true,
            fields: {
                name: { type: 'input', class: 'chat-name', nolabel: true, default: '' },
                text: { type: 'textarea', class: ' chat-text', nolabel: true, default: '' },
                other: {
                    type: 'fieldGroup',
                    class: 'field-group',
                    contentclass: "row",
                    tag: 'div',
                    nolabel: true,
                    fields: {
                        animation: { type: 'input', class: ' chat-animation col-4', nolabel: true, default: '' },
                        audio_link: { type: 'audio', class: ' chat-audio col-4', nolabel: true, default: '' },
                        float: { type: 'select', class: 'col-4 float-select', options: [
                            {value: 'left', label: 'Слева'}, {value: 'right', label: 'Справа'}
                        ], nolabel: true, default: 'left', onchange: (e) => $(e.delegateTarget).closest('.chat-message').removeClass('left right').addClass($(e.delegateTarget).val()) },
                    }
                },
            }
        },
    }
};

const answerQuestionItemConfig = {
    label: 'Вопрос-ответ',
    class: 'answer-question-block border mt-2 p-2 row rounded w-75',
    render: (e) => {$(e).removeClass('left right').addClass($(e).find('.float-select select').val())},
    tag: 'div',
    fields: {
        image: {
            type: 'fieldGroup',
            class: 'field-group col-3',
            tag: 'div',
            nolabel: true,
            fields: {
                image: { type: 'image', class: 'chat-avatar', nolabel: true, default: '' },
            }
        },
        name: {
            type: 'fieldGroup',
            class: 'field-group col-9',
            tag: 'div',
            nolabel: true,
            fields: {
                text: { type: 'textarea', class: ' chat-text', nolabel: true, default: '' },
                other: {
                    type: 'fieldGroup',
                    class: 'field-group',
                    contentclass: "row",
                    tag: 'div',
                    nolabel: true,
                    fields: {
                        animation: { type: 'input', class: ' chat-animation col-4', nolabel: true, default: '' },
                        float: { type: 'select', class: 'col-4 float-select', options: [
                            {value: 'left', label: 'Слева'}, {value: 'right', label: 'Справа'}
                        ], nolabel: true, default: 'left', onchange: (e) => $(e.delegateTarget).closest('.answer-question-block').removeClass('left right').addClass($(e.delegateTarget).val()) },
                    }
                },
            }
        },
    }
}
const tableCellConfig = {
    label: 'Ячейка',
    class: 'border p-2 rounded',
    tag: 'div',
    fields: {
        text: { type: 'textarea', class: '', nolabel: true, default: '' },
        audio_link: { type: 'audio', class: ' col-4', nolabel: true, default: '' },
    }
}
const tableHeaderCellConfig = {
    label: 'Ячейка',
    class: 'border p-2 rounded',
    tag: 'div',
    fields: {
        text: { type: 'textarea', class: '', nolabel: true, default: '' },
    }
}
const tableRowConfig = {
    label: 'Строка',
    class: 'border mt-2 p-2 rounded',
    tag: 'div',
    fields: {
        column_list: { label: 'Ячейки', type: 'array', class: 'mt-2', contentclass:"d-flex show", collapsible: true, itemConfig: tableCellConfig },
    }
}
const gridItemConfig = {
    label: 'Блок',
    class: 'col column-item border rounded',
    render: (e) => {$(e).parent().removeClass('row-cols-1 row-cols-2 row-cols-3 row-cols-4 row-cols-5').addClass('row-cols-'+$('.editor').find('.grid-columns-count select').val())},
    tag: 'div',
    fields: {
        image: { type: 'image', class: '', nolabel: true, default: '' },
        text: { type: 'textarea', class: ' ', nolabel: true, default: '' },
        animation: { type: 'input', class: ' ', nolabel: true, default: '' },
    }
};

const checkboxListItemConfig = {
    label: 'Галочка',
    class: 'col-12 column-item border rounded',
    tag: 'div',
    fields: {
        text: { type: 'textarea', class: ' ', nolabel: true, default: '' },
        animation: { type: 'input', class: ' ', nolabel: true, default: '' },
    }
};
const radioListItemConfig = {
    label: 'Вопрос-блок',
    class: 'col-12 column-item border rounded',
    tag: 'div',
    fields: {
        title: { type: 'input', class: ' ', nolabel: true, default: '' },
        text: { type: 'textarea', class: ' ', nolabel: true, default: '' },
        animation: { type: 'input', class: ' ', nolabel: true, default: '' },
    }
};
const listItemConfig = {
    label: 'Строка',
    class: 'col-12 pb-2',
    tag: 'div',
    fields: {
        text: { type: 'textarea', class: ' ', nolabel: true, default: '' },
        animation: { type: 'input', class: ' ', nolabel: true, default: '' },
    }
};

const variantOptionConfig = {
    label: 'Вариант ответа',
    class: '',
    tag: 'div',
    fields: {
        text: { label: 'Вариант ответа',  type: 'textarea', class: '', nolabel: true, default: '' },
    }
};
const chatOptionConfig = {
    label: 'Вариант ответа',
    class: '',
    tag: 'div',
    fields: {
        text: { label: 'Вариант ответа',  type: 'textarea', class: '', nolabel: true, default: '' },
    }
};
const variantItemConfig = {
    label: 'Вариант',
    class: 'card p-2 mt-2',
    tag: 'div',
    fields: {
        mode: { type: 'hidden', class: '', nolabel: true, default: 'variant' },
        type:  { type: 'hidden', class: '', nolabel: true, default: 'input' },
        index: { label: 'Номер', type: 'number',  class: '', default: 1 },
        answer: { label: 'Правильный ответ', type: 'input', class: '', default: '' },
        variants: { label: 'Варианты ответа', type: 'array', class: '', default: [], contentclass:"", nolabel: true, collapsible: true, itemConfig: variantOptionConfig }
    }
};
const matchItemConfig = {
    label: 'Сопоставление',
    class: 'card p-2 mt-2',
    tag: 'div',
    fields: {
        mode: { type: 'hidden', class: '', nolabel: true, default: 'match' },
        type:  { type: 'hidden', class: '', nolabel: true, default: 'input' },
        index: { label: 'Номер', type: 'number', class: '', default: 1 },
        answer: { label: 'Ответ', type: 'input', class: '', default: '' }
    }
};
const chatItemConfig = {
    label: 'Ответ в чате',
    class: 'card p-2 mt-2',
    tag: 'div',
    fields: {
        mode: { type: 'hidden', class: '', nolabel: true, default: 'chat' },
        type:  { type: 'hidden', class: '', nolabel: true, default: 'chat_textbox' },
        index: { label: 'Номер', type: 'number',  class: '', default: 1 },
        tip: { label: 'Подсказка', type: 'input', class: '', default: '' },
        answer: { label: 'Правильный ответ', type: 'input', class: '', default: '' },
        variants: { label: 'Варианты ответа', type: 'array', class: '', contentclass:"", nolabel: true, collapsible: true, itemConfig: chatOptionConfig }
    }
}
const checkboxItemConfig = {
    label: 'Галочка',
    class: 'card p-2 mt-2',
    tag: 'div',
    fields: {
        mode: { type: 'hidden', class: '', nolabel: true, default: 'simple' },
        type:  { type: 'hidden', class: '', nolabel: true, default: 'checkbox' },
        index: { label: 'Номер', type: 'number',  class: '', default: 1 },
        label: { label: 'Лейбл', type: 'input', class: '', default: '' },
        answer: { label: 'Правильный ответ', type: 'input', class: '', default: true },
    }
}
const radioItemConfig = {
    label: 'Ответ для теста',
    class: 'card p-2 mt-2',
    tag: 'div',
    fields: {
        index: { label: 'Номер', type: 'number',  class: '', default: 1 },
        type:  { type: 'hidden', class: '', nolabel: true, default: 'radio' },
        answer: { label: 'Правильный ответ', type: 'input', class: '', default: "" },
        mode:  { label: 'Режим', type: 'select', class: 'col-8', options: [
            {value: 'variant', label: 'Горизонтально'}, 
            {value: 'match', label: 'Вертикально'}
        ], default: 'variant' },
        variants: { label: 'Варианты ответа', type: 'array', class: '', contentclass:"", nolabel: true, collapsible: true, itemConfig: chatOptionConfig }
    }
}
const radioTestItemConfig = {
    label: 'Ответ для теста',
    class: 'card p-2 mt-2',
    tag: 'div',
    fields: {
        index: { label: 'Номер', type: 'number',  class: '', default: 1 },
        type:  { type: 'hidden', class: '', nolabel: true, default: 'radio' },
        answer: { label: 'Правильный ответ', type: 'input', class: '', default: "" },
        mode: { type: 'hidden', class: '', nolabel: true, default: 'match' },
        variants: { label: 'Варианты ответа', type: 'array', class: '', contentclass:"", nolabel: true, collapsible: true, itemConfig: chatOptionConfig }
    }
}

const formTemplates = {
    none: null,
    variant: variantItemConfig,
    match: matchItemConfig,
    chat: chatItemConfig,
    checkbox: checkboxItemConfig,
    radio: radioItemConfig
};


const config = {
    title: { label: 'Заголовок', type: 'input', class: 'col-6', default: 'Diñleyik, tekrarlayıq', onchange: () => renderPageList() },
    subtitle: { label: 'Подзаголовок', type: 'input', class: 'col-6', default: '', onchange: () => renderPageList() },
    image: {
        label: 'Изображение',
        type: 'fieldGroup',
        contentclass:"row",
        class: 'mt-2',
        tag: 'div',
        collapsible: true,
        fields: {
            image: { type: 'image', class: 'main-image', nolabel: true, default: '' },
        }
    },
    other: {
        label: 'Прочее',
        type: 'fieldGroup',
        contentclass:"row align-items-end",
        class: 'mt-2',
        tag: 'div',
        collapsible: true,
        fields: {
            audio: { label: 'Есть озвучка', type: 'checkbox', class: 'col-6', default: true },
            index: { label: 'Номер страницы', type: 'number', class: 'col-6', default: 1 },
        }
    },
    templates: {
        label: 'Содержимое',
        type: 'fieldGroup',
        contentclass:"row align-items-end",
        class: 'mt-2',
        tag: 'div',
        fields: {
            page_template: { label: 'Тип страницы', type: 'select', class: 'col-8', options: [
                {value: 'none', label: 'Не выбран'}, 
                {value: 'listSimple', label: 'Простой список'},
                {value: 'dialogue', label: 'Диалог'}, 
                {value: 'grid', label: 'Блоки'}, 
                {value: 'answerQuestion', label: 'Вопросы-ответы'}, 
                {value: 'chat', label: 'Чат'}, 
                {value: 'table', label: 'Таблица'}, 
                {value: 'checkboxes', label: 'Галочки'}, 
                {value: 'radio', label: 'Вопрос-блок'}
            ], default: 'none' },
            form_template: { label: 'Тип полей', type: 'select', class: 'col-4', options: [
                {value: 'none', label: 'Без полей'}, 
                {value: 'variant', label: 'Варианты ответа'}, 
                {value: 'match', label: 'Сопоставление'}, 
                {value: 'chat', label: 'Ответ в чате'}, 
                {value: 'checkbox', label: 'Галочки (А и Б)'}, 
                {value: 'radio', label: 'Галочки (А или Б)'}, 
            ], default: 'none' },
        }
    },
    template_config: {
        type: 'dynamic',
        fields: {
            none: null,
            dialogue: {
                replica_list: { label: 'Реплики', type: 'array', class: 'col-8 mt-2', contentclass:"row ms-2 show", collapsible: true, itemConfig: replicaItemConfig },
                input_list: { label: 'Поля',type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            },
            grid: {
                grid_columns_count: { label: 'Количество блоков в ряду', type: 'select', class: 'col-8 grid-columns-count mt-2', options: [
                    {value: 1, label: '1'}, {value: 2, label: '2'}, {value: 3, label: '3'}, {value: 4, label: '4'}, {value: 5, label: '5'}
                ], default: 3, onchange: (e) => $(e.delegateTarget).closest('.editor').find('.column-item').parent().removeClass('row-cols-1 row-cols-2 row-cols-3 row-cols-4 row-cols-5').addClass('row-cols-'+$(e.delegateTarget).val())  },
                column_list: { label: 'Блоки', type: 'array', class: 'col-8 mt-2', contentclass:"row ms-2 show", collapsible: true, itemConfig: gridItemConfig },
                input_list: { label: 'Поля', type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            },
            answerQuestion: {
                block_list: { label: 'Блоки', type: 'array', class: 'col-8 mt-2', contentclass:"row ms-2 show", collapsible: true, itemConfig: answerQuestionItemConfig },
                input_list: { label: 'Поля', type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            },
            chat: {
                replica_list: { label: 'Реплики', type: 'array', class: 'col-8 mt-2', contentclass:"row ms-2 show", collapsible: true, itemConfig: replicaItemConfig },
                input_list: { label: 'Поля',type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            },
            table:{
                table_header: { 
                    type: 'fieldGroup',
                    label: 'Строка заголовка',
                    class: 'field-group mt-2 p-2 rounded col-8',
                    tag: 'div',
                    fields: {
                        table_header: { label: 'Ячейки заголовка', type: 'array', class: 'col-12 mt-2', contentclass:"d-flex show", collapsible: true, itemConfig: tableCellConfig },
                    } 
                },
                row_list: { label: 'Строки', type: 'array', class: 'col-8 mt-2', contentclass:"show", collapsible: true, itemConfig: tableRowConfig },
                input_list: { label: 'Поля',type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            },
            checkboxes: {
                checkboxes_list: { label: 'Галочки', type: 'array', class: 'col-8 mt-2', contentclass:"row ms-2 show", collapsible: true, itemConfig: checkboxListItemConfig },
                input_list: { label: 'Поля', type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            },
            radio: {
                radio_list: { label: 'Вопросы', type: 'array', class: 'col-8 mt-2', contentclass:"row show", collapsible: true, itemConfig: radioListItemConfig },
                input_list: { label: 'Поля', type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            },
            listSimple: {
                item_list: { label: 'Элементы списка', type: 'array', class: 'col-8 mt-2', contentclass:"row show", collapsible: true, itemConfig: listItemConfig },
                input_list: { label: 'Поля', type: 'array', class: 'col-4 mt-2', contentclass:"show", collapsible: true, itemConfig: null }
            }
        }
    }
};