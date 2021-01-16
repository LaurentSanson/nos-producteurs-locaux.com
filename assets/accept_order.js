jQuery(document).ready(() => {
    // Get the ul that holds the collection of tags
    let $slotsCollectionHolder = $('div.slots');
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $slotsCollectionHolder.data('index', $slotsCollectionHolder.find('input').length);

    $('body').on('click', '.add_item_link', function (e) {
        let $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
        // add a new tag form (see next code block)
        addFormToCollection($collectionHolderClass);
    })
});

function addFormToCollection($collectionHolderClass)
{
    let $collectionHolder = $('.' + $collectionHolderClass);
    let prototype = $collectionHolder.data('prototype');
    let index = $collectionHolder.data('index');
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index + 1);
    newForm = newForm.replace(/label__/g, '');
    $collectionHolder.data('index', index + 1);
    let $newFormLi = $('<p></p>').append(newForm);
    $collectionHolder.append($newFormLi)
}