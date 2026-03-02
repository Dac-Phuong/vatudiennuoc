import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import ToastService from "primevue/toastservice";
import ConfirmationService from "primevue/confirmationservice";
import ConfirmDialog from "primevue/confirmdialog";

// Components
import Toast from "primevue/toast";
import Button from "primevue/button";
import DatePicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup";
import Row from "primevue/row";
import Dialog from "primevue/dialog";
import AutoComplete from "primevue/autocomplete";
import Slider from "primevue/slider";
import Editor from "primevue/editor";
import ConfirmPopup from "primevue/confirmpopup";
import Drawer from "primevue/drawer";
import Accordion from "primevue/accordion";
import AccordionPanel from "primevue/accordionpanel";
import AccordionHeader from "primevue/accordionheader";
import AccordionContent from "primevue/accordioncontent";
import RadioButton from "primevue/radiobutton";
import RadioButtonGroup from "primevue/radiobuttongroup";
import Select from "primevue/select";
import Badge from "primevue/badge";
import OverlayBadge from "primevue/overlaybadge";
import Carousel from "primevue/carousel";
import Galleria from "primevue/galleria";
import Image from "primevue/image";
import Checkbox from "primevue/checkbox";
import CheckboxGroup from "primevue/checkboxgroup";
import StyleClass from "primevue/styleclass";
import Message from "primevue/message";
import Textarea from "primevue/textarea";
import Tag from "primevue/tag";
import Menu from "primevue/menu";
import MultiSelect from "primevue/multiselect";
import Chip from "primevue/chip";
import Chart from "primevue/chart";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import InputNumber from "primevue/inputnumber";
import Card from "primevue/card";
import FileUpload from "primevue/fileupload";
import Tooltip from "primevue/tooltip";
import ProgressBar from "primevue/progressbar";
import ToggleButton from 'primevue/togglebutton';
import TabView from 'primevue/tabview';
import Divider from 'primevue/divider';
import ToggleSwitch from 'primevue/toggleswitch';
import Avatar from 'primevue/avatar';
import AvatarGroup from 'primevue/avatargroup'; 
import Paginator from 'primevue/paginator';
import FloatLabel from 'primevue/floatlabel';
import Password from 'primevue/password';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Dropdown from 'primevue/dropdown';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

export default {
    install(app) {
        app.use(PrimeVue, {
            ripple: true,
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: ".app-dark",
                },
            },
        });

        app.use(ToastService);
        app.use(ConfirmationService);

        // Đăng ký component
        app.component("Toast", Toast);
        app.component("Button", Button);
        app.component("DatePicker", DatePicker);
        app.component("Slider", Slider);
        app.component("InputText", InputText);
        app.component("DataTable", DataTable);
        app.component("Column", Column);
        app.component("ColumnGroup", ColumnGroup);
        app.component("Row", Row);
        app.component("Dialog", Dialog);
        app.component("AutoComplete", AutoComplete);
        app.component("Editor", Editor);
        app.component("ConfirmPopup", ConfirmPopup);
        app.component("Drawer", Drawer);
        app.component("Accordion", Accordion);
        app.component("AccordionPanel", AccordionPanel);
        app.component("AccordionHeader", AccordionHeader);
        app.component("AccordionContent", AccordionContent);
        app.component("RadioButton", RadioButton);
        app.component("RadioButtonGroup", RadioButtonGroup);
        app.component("Select", Select);
        app.component("OverlayBadge", OverlayBadge);
        app.component("Badge", Badge);
        app.component("Carousel", Carousel);
        app.component("Galleria", Galleria);
        app.component("Image", Image);
        app.component("Checkbox", Checkbox);
        app.component("CheckboxGroup", CheckboxGroup);
        app.directive("styleclass", StyleClass);
        app.component("Message", Message);
        app.component("Textarea", Textarea);
        app.component("Tag", Tag);
        app.component("Menu", Menu);
        app.component("ConfirmDialog", ConfirmDialog);
        app.component("MultiSelect", MultiSelect);
        app.component("Chip", Chip);
        app.component("Chart", Chart);
        app.component("Tabs", Tabs);
        app.component("TabList", TabList);
        app.component("Tab", Tab);
        app.component("TabPanels", TabPanels);
        app.component("TabPanel", TabPanel);
        app.component("InputNumber", InputNumber);
        app.component("Card", Card);
        app.component("FileUpload", FileUpload);
        app.component("ProgressBar", ProgressBar);
        app.component("ToggleButton", ToggleButton);
        app.component("TabView", TabView);
        app.component("Divider", Divider);
        app.component("ToggleSwitch", ToggleSwitch);
        app.component("Avatar", Avatar);
        app.component("Paginator", Paginator);
        app.component("AvatarGroup", AvatarGroup);
        app.component("FloatLabel", FloatLabel);
        app.component("Password", Password);
        app.component("InputGroup", InputGroup);
        app.component("InputGroupAddon", InputGroupAddon);
        app.component("Dropdown", Dropdown);
        app.component("IconField", IconField);
        app.component("InputIcon", InputIcon);
        app.directive("tooltip", Tooltip);
    },
};
