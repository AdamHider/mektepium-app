<?= $this->extend('layouts/'.$settings['layout']) ?>
<?= $this->section('content') ?>
<div class="container pb-3">
    <form action="/admin/lessons/save<?= $lesson ? '/' . $lesson['id'] : '' ?>" method="post">
        <div class="d-flex justify-content-end mb-3">
            <button type="submit" class="btn btn-primary rounded-3">Сохранить</button>
        </div>
        <?php if (session()->getFlashdata('status')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('status') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="row">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-common-tab" data-bs-toggle="tab" data-bs-target="#nav-common" type="button" role="tab" aria-controls="nav-common" aria-selected="true">Главная</button>
                    <button class="nav-link" id="nav-pages-tab" data-bs-toggle="tab" data-bs-target="#nav-pages" type="button" role="tab" aria-controls="nav-pages" aria-selected="false">Страницы</button>
                    <button class="nav-link" id="nav-cost-tab" data-bs-toggle="tab" data-bs-target="#nav-cost" type="button" role="tab" aria-controls="nav-cost" aria-selected="false">Стоимость</button>
                    <button class="nav-link" id="nav-other-tab" data-bs-toggle="tab" data-bs-target="#nav-other" type="button" role="tab" aria-controls="nav-other" aria-selected="false">Прочее</button>
                </div>
            </nav>
            <div class="tab-content rounded-3 shadow-sm border bg-white p-4 ">
                <div class="tab-pane fade show active" id="nav-common" role="tabpanel" aria-labelledby="nav-common-tab">
                    <div class="form-group mt-2">
                        <label for="title">Название</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?= $lesson['title'] ?? '' ?>" required>
                    </div>
                    <div class="form-group mt-2">
                        <label for="description">Описание</label>
                        <textarea type="text" name="description" id="description" class="form-control" value="<?= $lesson['description'] ?? '' ?>" required><?= $lesson['description'] ?? '' ?></textarea>
                    </div>
                    <div class="form-group mt-2">
                        <label for="course_id">Курс</label>
                        <select name="course_id" class="form-select" id="course_id" value="<?= $lesson['course_id'] ?? '' ?>" required>
                            <option disabled value selected>---Не выбрано---</option>
                            <?php foreach($courses as $course) : ?>
                                <?php if(!empty($lesson['course_id']) && $course['id'] == $lesson['course_id']) : ?>
                                    <option value="<?= $course['id'] ?>" selected><?= $course['title'] ?></option>
                                <?php else: ?>   
                                    <option value="<?= $course['id'] ?>"><?= $course['title'] ?></option>
                                <?php endif; ?>   
                            <?php endforeach; ?>    
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="course_section_id">Раздел курса</label>
                        <select name="course_section_id" class="form-select" id="course_section_id" value="<?= $lesson['course_section_id'] ?? '' ?>" required>
                            <option disabled value selected>---Не выбрано---</option>
                            <?php foreach($course_sections as $course_section) : ?>
                                <?php if(!empty($lesson['course_section_id']) && $course_section['id'] == $lesson['course_section_id']) : ?>
                                    <option value="<?= $course_section['id'] ?>" selected><?= $course_section['title'] ?></option>
                                <?php else: ?>   
                                    <option value="<?= $course_section['id'] ?>"><?= $course_section['title'] ?></option>
                                <?php endif; ?>   
                            <?php endforeach; ?>    
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="language_id">Язык</label>
                        <select name="language_id" class="form-select" id="language_id" value="<?= $lesson['language_id'] ?? '' ?>" required>
                            <option disabled value selected>---Не выбрано---</option>
                            <?php foreach($languages as $language) : ?>
                                <?php if(!empty($lesson['language_id']) && $language['id'] == $lesson['language_id']) : ?>
                                    <option value="<?= $language['id'] ?>" selected><?= $language['title'] ?></option>
                                <?php else: ?>   
                                    <option value="<?= $language['id'] ?>"><?= $language['title'] ?></option>
                                <?php endif; ?>   
                            <?php endforeach; ?>    
                        </select>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-4">
                            <label for="image">Изображение</label>
                            <div class="card ficker-image text-center image-input">
                                <img src="<?= $lesson['image'] ?? '' ?>" class="card-img">
                                <div class="card-footer">
                                    <button class="btn btn-outline-secondary pick-image" type="button">Choose</button>
                                </div>
                                <input type="hidden" name="image" class="form-control" value="<?= $lesson['image'] ?? '' ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                    
                <div class="tab-pane fade" id="nav-pages" role="tabpanel" aria-labelledby="nav-pages-tab">
                    <div class="form-group mt-2">
                        <label for="type">Тип</label>
                        <select class="form-select" name="type" id="type" required>
                            <option disabled value selected>---Не выбрано---</option>
                            <?php if(!empty($lesson['type']) && $lesson['type'] == 'common') : ?>
                                <option value="common" selected>Общий</option>
                                <option value="lexis">Лексика</option>
                                <option value="grammar">Грамматика</option>
                            <?php elseif(!empty($lesson['type']) && $lesson['type'] == 'lexis') : ?>   
                                <option value="common">Общий</option>
                                <option value="lexis" selected>Лексика</option>
                                <option value="grammar">Грамматика</option>
                            <?php elseif(!empty($lesson['type']) && $lesson['type'] == 'grammar') : ?>   
                                <option value="common">Общий</option>
                                <option value="lexis">Лексика</option>
                                <option value="grammar" selected>Грамматика</option>
                            <?php else : ?>   
                                <option value="common">Общий</option>
                                <option value="lexis">Лексика</option>
                                <option value="grammar">Грамматика</option>
                            <?php endif; ?> 
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="pages">Страницы</label>
                        <input type="hidden" name="pages" id="pages" class="form-control" value="<?= esc($lesson['pages']) ?? '' ?>" required/>
                    </div>
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#pagesModal">
                        Редактор страниц
                    </button>
                    <div class="modal fade" id="pagesModal" tabindex="-1" aria-labelledby="pagesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="pagesModalLabel">Редактор страниц</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <?= view('admin/lessons/page_manager', ['pages' => $lesson['pages']]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="tab-pane fade" id="nav-cost" role="tabpanel" aria-labelledby="nav-cost-tab">
                    <div class="row">
                        <div class="col-6 form-group mt-2">
                            <label for="cost_resource_config" class="mb-2">Конфигурация стоимости</label>
                            <input type="hidden" name="cost_config" id="cost_resource_config" class="form-control" value="<?= esc($lesson['cost_config']) ?? '' ?>">
                            <?php foreach($resources as $resource) : ?>
                                <div class="mb-2 cost-resources resource">
                                    <label for="costResouce<?=$resource['code']?>" class="form-label"><?= $resource['code'] ?></label>
                                    <input type="text" class="form-control" data-code="<?=$resource['code']?>" id="costResouce<?=$resource['code']?>" value="<?=json_decode($lesson['cost_config'], true)[$resource['code']] ?? 0?>">
                                </div>
                            <?php endforeach; ?> 
                        </div>
                        <div class="col-6  form-group mt-2">
                            <label for="reward_resource_config" class="mb-2">Конфигурация наград</label>
                            <input type="hidden" name="reward_config" id="reward_resource_config" class="form-control" value="<?= esc($lesson['reward_config']) ?? '' ?>">
                            <?php foreach($resources as $resource) : ?>
                                <div class="mb-2 reward-resources resource">
                                    <label for="rewardResouce<?=$resource['code']?>" class="form-label"><?= $resource['code'] ?></label>
                                    <input type="text" class="form-control" data-code="<?=$resource['code']?>" id="rewardResouce<?=$resource['code']?>" value="<?=json_decode($lesson['reward_config'], true)[$resource['code']] ?? 0?>">
                                </div>
                            <?php endforeach; ?> 
                        </div>
                    </div>
                </div>
                    
                <div class="tab-pane fade" id="nav-other" role="tabpanel" aria-labelledby="nav-other-tab">
                    <div class="form-group mt-2">
                        <label for="parent_id">Родительский урок</label>
                        <select name="parent_id" class="form-select" id="parent_id" value="<?= $lesson['parent_id'] ?? '' ?>">
                            <option disabled value selected>---Не выбрано---</option>
                            <?php foreach($parent_lessons as $parent_lesson) : ?>
                                <?php if(!empty($lesson['parent_id']) && $parent_lesson['id'] == $lesson['parent_id']) : ?>
                                    <option value="<?= $parent_lesson['id'] ?>" selected><?= $parent_lesson['title'] ?></option>
                                <?php else: ?>   
                                    <option value="<?= $parent_lesson['id'] ?>"><?= $parent_lesson['title'] ?></option>
                                <?php endif; ?>   
                            <?php endforeach; ?>    
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="unblock_after">Разблокировать после</label>
                        <select name="unblock_after" class="form-select" id="unblock_after" value="<?= $lesson['unblock_after'] ?? '' ?>">
                            <option disabled value selected>---Не выбрано---</option>
                            <?php foreach($unblock_lessons as $unblock_lesson) : ?>
                                <?php if(!empty($lesson['unblock_after']) && $unblock_lesson['id'] == $lesson['unblock_after']) : ?>
                                    <option value="<?= $unblock_lesson['id'] ?>" selected><?= $unblock_lesson['title'] ?></option>
                                <?php else: ?>   
                                    <option value="<?= $unblock_lesson['id'] ?>"><?= $unblock_lesson['title'] ?></option>
                                <?php endif; ?>   
                            <?php endforeach; ?>    
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="published">Опубликован</label>
                        <select class="form-select" name="published" id="published" required>
                            <?php if(!empty($lesson['published']) && $lesson['published'] == 1) : ?>
                                <option value="0">Нет</option>
                                <option value="1" selected>Да</option>
                            <?php else: ?>   
                                <option value="0" selected>Нет</option>
                                <option value="1">Да</option>
                            <?php endif; ?> 
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="is_private">Приватный</label>
                        <select class="form-select" name="is_private" id="is_private">
                            <?php if(!empty($lesson['published']) && $lesson['published'] == 1) : ?>
                                <option value="0">Нет</option>
                                <option value="1" selected>Да</option>
                            <?php else: ?>   
                                <option value="0" selected>Нет</option>
                                <option value="1">Да</option>
                            <?php endif; ?> 
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<link rel="stylesheet" href="<?=base_url('/assets/lesson_page_manager/css/main.css')?>" type="text/css">

<?= view('misc/filepicker') ?>

<script>
//$(document).ready(() => {
        
    $('.pick-image').on('click', (e) => {
        let input = $(e.delegateTarget).closest('.input-group').find('input')
        let container = $(e.delegateTarget).closest('.form-group');
        let modal = new bootstrap.Modal(document.getElementById('pickerModal'), {})
        initFileExplorer({
            filePickerElement: '#file_picker',
            multipleMode: false,
            pickerMode: true,
            onPicked: (url) => {
                $(container).find('input').val(url).trigger("input");
                $(container).find('img').prop('src', url)
                modal.hide()
            }
        });
        modal.show()
    })

    const sections = <?= !empty($course_sections) ? json_encode($course_sections) : '[]' ?>

    $('[name="course_id"]').on('change', (e) => {
        let value = $(e.delegateTarget).val()
        let sectionsFiltered = sections.filter((section) => section.course_id == value)
        $('[name="course_section_id"]').empty()
        $('[name="course_section_id"]').attr('value', 0)
        $('[name="course_section_id"]').append($(`<option>`).prop('disabled', true).prop('selected', 'selected').val('0').text('---Не выбрано---'))
        sectionsFiltered.forEach((section) => {
            let option = $(`<option>`).val(section.id).text(section.title)
            $('[name="course_section_id"]').append(option)
        })
    })
    $('[name="course_id"]').trigger('change')


    var costConfig = <?= !empty($lesson['cost_config']) ? $lesson['cost_config'] : '[]' ?> 
    var rewardConfig = <?= !empty($lesson['reward_config']) ? $lesson['reward_config'] : '[]' ?> 

    $('.cost-resources input').on('change', (e) => {
        let code = $(e.delegateTarget).attr('data-code')
        let quantity = $(e.delegateTarget).val()
        costConfig[code] = quantity
        if(quantity == 0){
            delete costConfig[code]
        }
        $('[name="cost_config"]').val(JSON.stringify(costConfig))
        renderResources()
    })
    $('.reward-resources input').on('change', (e) => {
        let code = $(e.delegateTarget).attr('data-code')
        let quantity = $(e.delegateTarget).val()
        rewardConfig[code] = quantity
        if(quantity == 0){
            delete rewardConfig[code]
        }
        $('[name="reward_config"]').val(JSON.stringify(rewardConfig))
        renderResources()
    })
    function renderResources(){
        $('.cost-resources input, .reward-resources input').each((index, el) => {
            if($(el).val()*1 > 0){
                $(el).closest('.resource').addClass('positive')
            } else {
                $(el).closest('.resource').removeClass('positive')
            }
        })
    }
    renderResources()
//})
</script>
<style>
.cost-resources.resource, .reward-resources.resource{
    color: gray;
}
.resource label{
    text-transform: capitalize;
}
.cost-resources.resource.positive{
    color: red;
    font-weight: bold;
}
.cost-resources.resource.positive input{
    color: red;
}
.reward-resources.resource.positive{
    color: green;
    font-weight: bold;
}
.reward-resources.resource.positive input{
    color: green;
}

</style>
<?= $this->endSection() ?>