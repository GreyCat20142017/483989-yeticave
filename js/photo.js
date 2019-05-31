'use strict';

(function () {
  var FILE_TYPES = ['jpg', 'jpeg', 'png'];
  var HIDDEN_CLASSNAME = 'visually-hidden';
  var MAX_SIZE = 200000;


  var getElementBySelector = function (parentObject, selector) {
    if (parentObject) {
      return parentObject.querySelector(selector);
    }
    return false;
  };

  var removeClassName = function (element, className) {
    if (element && element.classList.contains(className)) {
      element.classList.remove(className);
    }
  };

  var addClassName = function (element, className) {
    if (element && !element.classList.contains(className)) {
      element.classList.add(className);
    }
  };

  var loadPicture = function () {
    if (linkWrapper && linkUploadFile && linkImgPreview) {
      var file = linkUploadFile.files[0];
      var fileName = file.name.toLowerCase();
      var matches = FILE_TYPES.some(function (item) {
        return fileName.endsWith(item);
      });
      if (matches && (file.size <= MAX_SIZE)) {
        var reader = new FileReader();
        reader.addEventListener('load', function () {
          linkImgPreview.src = reader.result;
          linkImgPreview.title = 'Для удаления превью необходимо кликнуть по правому верхнему углу изображения';
          linkWrapper.style.display = 'block';
          linkWrapper.style.zIndex = '10';
          linkDeleteFile.style.zIndex = '11';
        });
        reader.readAsDataURL(file);
      }
    }
  };

  var clearPicture = function () {
    if (linkUploadFile && linkImgPreview) {
      linkImgPreview.src = '';
      linkUploadFile.value = '';
      linkWrapper.style.display = 'none';
    }
  };

  var onUploadFileChange = function (evt) {
    evt.preventDefault();
    loadPicture();
  };

  var onClearFile = function (evt) {
    evt.preventDefault();
    clearPicture();
  };

  var endsWithSupport = function () {
    if (!String.prototype.endsWith) {
      String.prototype.endsWith = function(searchString, position) {
        var subjectString = this.toString();
        if (typeof position !== 'number' || !isFinite(position) || Math.floor(position) !== position || position > subjectString.length) {
          position = subjectString.length;
        }
        position -= searchString.length;
        var lastIndex = subjectString.indexOf(searchString, position);
        return lastIndex !== -1 && lastIndex === position;
      };
    }
  };

  endsWithSupport();

  var main = document.body;
  var container = getElementBySelector(main, '.form__item--file');
  var linkImgPreview = getElementBySelector(container, '.preview__img > img');
  var linkUploadFile = getElementBySelector(container, '#photo2');
  var linkDeleteFile = getElementBySelector(container, '.preview__remove');

  var linkWrapper = getElementBySelector(container, '.preview');

  if (linkUploadFile && linkImgPreview) {
    linkUploadFile.addEventListener('change', onUploadFileChange);
    linkDeleteFile.addEventListener('click', onClearFile);
  }

})();