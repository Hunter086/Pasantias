
            jQuery(document).ready(function() {
                // Get the ul that holds the collection of tags
                var $tagsCollectionHolder = $('ul.contacto');
                // count the current form inputs we have (e.g. 2), use that as the new
                // index when inserting a new item (e.g. 2)
                $tagsCollectionHolder.data('index', $tagsCollectionHolder.find('input').length);
    
                $('body').on('click', '.add_item_link', function(e) {
                    var $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
                    // add a new tag form (see next code block)
                    addFormToCollection($collectionHolderClass);
                    
                })
                $('body').on('click', 'remove_item_link', function(e) {
                    $collectionHolder = $('ul.contacto');
                     $collectionHolder.find('li').each(function() {
                    addTagFormDeleteLink($(this));
                
                });
                    
                })
            });
     
        
        
           function addFormToCollection($collectionHolderClass) {
                    // Get the ul that holds the collection of tags
                    var $collectionHolder = $('.' + $collectionHolderClass);
    
                    // Get the data-prototype explained earlier
                    var prototype = $collectionHolder.data('prototype');
    
                    // get the new index
                    var index = $collectionHolder.data('index');
                    var newForm = prototype;
                    // You need this only if you didn't set 'label' => false in your tags field in TaskType
                    // Replace '__name__label__' in the prototype's HTML to
                    // instead be a number based on how many items we have
                    // newForm = newForm.replace(/__name__label__/g, index);
    
                    // Replace '__name__' in the prototype's HTML to
                    // instead be a number based on how many items we have
                    newForm = newForm.replace(/__name__/g, index);
    
                    // increase the index with one for the next item
                    $collectionHolder.data('index', index + 1);
                    // Display the form in the page in an li, before the "Add a tag" link li
                    var $newFormLi = $('<li><h3>Contacto Otro</h3></li>').append(newForm);
                    // Add the new form at the end of the list
                    $collectionHolder.append($newFormLi)
                    addTagFormDeleteLink($newFormLi);
                }
               
                function addTagFormDeleteLink($tagFormLi) {
                    var $removeFormButton = $('<button class="btn btn-outline-info mt-2 mb-2" id="btn-contact" type="button"><img src="/PasantiasyBecasFuturxsProfesionales/img/svg/minus-solid.svg" alt="Quitar Contacto"></button> <hr>');
                    $tagFormLi.append($removeFormButton);
    
                    $removeFormButton.on('click', function(e) {
                        // remove the li for the tag form
                        $tagFormLi.remove();
                    });
                }