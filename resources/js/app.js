import './bootstrap';
import * as FilePond from 'filepond';
import 'filepond/dist/filepond.min.css';

import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';
import FilePondPluginImageEdit from 'filepond-plugin-image-edit';

import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

window.FilePond = FilePond;
window.FilePondPluginFileValidateType = FilePondPluginFileValidateType;
window.FilePondPluginImageExifOrientation = FilePondPluginImageExifOrientation;
window.FilePondPluginImagePreview = FilePondPluginImagePreview;
window.FilePondPluginImageCrop = FilePondPluginImageCrop;
window.FilePondPluginImageResize = FilePondPluginImageResize;
window.FilePondPluginImageTransform = FilePondPluginImageTransform;
window.FilePondPluginImageEdit = FilePondPluginImageEdit;