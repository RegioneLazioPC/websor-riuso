const firstNames = ["Sophie", "Isabelle", "Emily", "Olivia", "Lily", "Chloe", "Isabella",
    "Amelia", "Jessica", "Sophia", "Ava", "Charlotte", "Mia", "Lucy", "Grace", "Ruby",
    "Ella", "Evie", "Freya", "Isla", "Poppy", "Daisy", "Layla"];
const lastNames = ["Beckham", "Black", "Braxton", "Brennan", "Brock", "Bryson", "Cadwell",
    "Cage", "Carson", "Chandler", "Cohen", "Cole", "Corbin", "Dallas", "Dalton", "Dane",
    "Donovan", "Easton", "Fisher", "Fletcher", "Grady", "Greyson", "Griffin", "Gunner",
    "Hayden", "Hudson", "Hunter", "Jacoby", "Jagger", "Jaxon", "Jett", "Kade", "Kane",
    "Keating", "Keegan", "Kingston", "Kobe"];

const COUNTRY_CODES = {
    Ireland: "ie",
    Spain: "es",
    "United Kingdom": "gb",
    France: "fr",
    Germany: "de",
    Sweden: "se",
    Italy: "it",
    Greece: "gr",
    Iceland: "is",
    Portugal: "pt",
    Malta: "mt",
    Norway: "no",
    Brazil: "br",
    Argentina: "ar",
    Colombia: "co",
    Peru: "pe",
    Venezuela: "ve",
    Uruguay: "uy"
};

const countries = [
    {country: "Ireland", continent: "Europe", language: "English"},
    {country: "Spain", continent: "Europe", language: "Spanish"},
    {country: "United Kingdom", continent: "Europe", language: "English"},
    {country: "France", continent: "Europe", language: "French"},
    {country: "Germany", continent: "Europe", language: "(other)"},
    {country: "Sweden", continent: "Europe", language: "(other)"},
    {country: "Norway", continent: "Europe", language: "(other)"},
    {country: "Italy", continent: "Europe", language: "(other)"},
    {country: "Greece", continent: "Europe", language: "(other)"},
    {country: "Iceland", continent: "Europe", language: "(other)"},
    {country: "Portugal", continent: "Europe", language: "Portuguese"},
    {country: "Malta", continent: "Europe", language: "(other)"},
    {country: "Brazil", continent: "South America", language: "Portuguese"},
    {country: "Argentina", continent: "South America", language: "Spanish"},
    {country: "Colombia", continent: "South America", language: "Spanish"},
    {country: "Peru", continent: "South America", language: "Spanish"},
    {country: "Venezuela", continent: "South America", language: "Spanish"},
    {country: "Uruguay", continent: "South America", language: "Spanish"}
];

const addresses = [
    '1197 Thunder Wagon Common, Cataract, RI, 02987-1016, US, (401) 747-0763',
    '3685 Rocky Glade, Showtucket, NU, X1E-9I0, CA, (867) 371-4215',
    '3235 High Forest, Glen Campbell, MS, 39035-6845, US, (601) 638-8186',
    '2234 Sleepy Pony Mall , Drain, DC, 20078-4243, US, (202) 948-3634',
    '2722 Hazy Turnabout, Burnt Cabins, NY, 14120-5642, US, (917) 604-6597',
    '6686 Lazy Ledge, Two Rock, CA, 92639-3020, US, (619) 901-9911',
    '2000 Dewy Limits, Wacahoota, NF, A4L-2V9, CA, (709) 065-3959',
    '7710 Noble Pond Avenue, Bolivia, RI, 02931-1842, US, (401) 865-2160',
    '3452 Sunny Vale, Pyro, ON, M8V-4Z0, CA, (519) 072-8609',
    '4402 Dusty Cove, Many Farms, UT, 84853-8223, US, (435) 518-0673',
    '5198 Silent Parade, Round Bottom, MD, 21542-9798, US, (301) 060-7245',
    '8550 Shady Moor, Kitty Fork, CO, 80941-6207, US, (303) 502-3767',
    '2131 Old Dell, Merry Midnight, AK, 99906-8842, US, (907) 369-2206',
    '7390 Harvest Crest, Mosquito Crossing, RI, 02957-6116, US, (401) 463-6348',
    '874 Little Point, Hot Coffee, BC, V3U-2P6, CA, (250) 706-9207',
    '8834 Stony Pioneer Heights, Newlove, OR, 97419-8670, US, (541) 408-2213',
    '9829 Grand Beach, Flint, UT, 84965-9900, US, (435) 700-5161',
    '3799 Cozy Blossom Ramp, Ptarmigan, MS, 38715-0313, US, (769) 740-1526',
    '3254 Silver Island Loop, Maunaloa, DE, 19869-3169, US, (302) 667-7671',
    '1081 Middle Wood, Taylors Gut Landing, OR, 97266-2873, US, (541) 357-6310',
    '1137 Umber Trail, Shacktown, NW, X3U-5Y8, CA, (867) 702-6883',
    '9914 Hidden Bank, Wyoming, MO, 64635-9665, US, (636) 280-4192',
    '7080 Misty Nectar Townline, Coward, AB, T9U-3N4, CA, (403) 623-2838',
    '1184 Wishing Grounds, Vibank, NW, X7D-0V9, CA, (867) 531-2730',
    '126 Easy Pointe, Grandview Beach, KY, 40928-9539, US, (502) 548-0956',
    '6683 Colonial Street, Swan River, BC, V1A-9I8, CA, (778) 014-4257',
    '960 Gentle Oak Lane, Shakopee, ND, 58618-6277, US, (701) 327-1219',
    '6918 Cotton Pine Corner, Kenaston, IA, 52165-3975, US, (515) 906-7427',
    '2368 Burning Woods, Ernfold, NY, 11879-9186, US, (646) 819-0355',
    '5646 Quiet Shadow Chase, Tiger Tail, IA, 52283-5537, US, (712) 375-9225',
    '5466 Foggy Mountain Dale, Sweet Home, MT, 59738-0251, US, (406) 881-1706',
    '5313 Clear Willow Route, Amazon, BC, V0S-2S6, CA, (604) 340-7596',
    '7000 Pleasant Autoroute, Spaceport City, UT, 84749-2448, US, (435) 154-3360',
    '8359 Quaking Anchor Road, Gross, BC, V9O-0H5, CA, (250) 985-3859',
    '5143 Amber Deer Hollow, New Deal, ND, 58446-0853, US, (701) 927-0322',
    '6230 Jagged Bear Key, Young, AR, 72337-3811, US, (501) 805-7239',
    '7207 Heather Vista, Devon, WY, 82520-1771, US, (307) 358-7092',
    '9416 Red Rise Place, Spraytown, OK, 73809-4766, US, (580) 867-1973',
    '3770 Golden Horse Diversion, Yelland, IL, 60471-1487, US, (224) 717-9349',
    '4819 Honey Treasure Park, Alaska, NB, E1U-3I0, CA, (506) 656-9138',
    '6187 Round Front, Land O Lakes, AK, 99873-6403, US, (907) 853-9063',
    '9218 Crystal Highway, Pickelville, MT, 59847-9299, US, (406) 076-0024',
    '6737 Bright Quay, Lazy Mountain, KY, 42390-4772, US, (606) 256-7288',
    '237 Merry Campus, Twentysix, SC, 29330-4909, US, (864) 945-0157',
    '446 Fallen Gate Rise, Petrolia, SC, 29959-9527, US, (864) 826-0553',
    '2347 Indian Boulevard, Frisbee, VA, 23797-6458, US, (703) 656-8445',
    '365 Emerald Grove Line, Level, NC, 28381-1514, US, (919) 976-7958',
    '1207 Iron Extension, Klickitat, SC, 29197-8571, US, (803) 535-7888',
    '6770 Cinder Glen, Caronport, OH, 45053-5002, US, (440) 369-4018',
    '7619 Tawny Carrefour, Senlac, NV, 89529-9876, US, (775) 901-6433'];

const IT_SKILLS = ['android', 'css', 'html5', 'mac', 'windows'];
const IT_SKILLS_NAMES = ['Android', 'CSS', 'HTML 5', 'Mac', 'Windows'];

const columnDefs = [
    {
        headerName: '', width: 30, checkboxSelection: true,
        suppressMenu: true, pinned: true
    },
    {
        headerName: 'Employee',
        children: [
            {
                headerName: "Name", field: "name",
                width: 150, pinned: true, sortable: true
            },
            {
                headerName: "Country", field: "country", width: 150,
                cellRenderer: countryCellRenderer, pinned: true, sortable: true,
                filterParams: {cellRenderer: countryCellRenderer, cellHeight: 20}
            },
        ]
    },
    {
        headerName: 'IT Skills',
        children: [
            {
                headerName: "Skills",
                width: 125,
                cellRenderer: skillsCellRenderer,
                filter: SkillFilter
            },
            {
                headerName: "Proficiency",
                field: "proficiency",
                width: 120, sortable: true,
                cellRenderer: percentCellRenderer,
                filter: ProficiencyFilter
            },
        ]
    },
    {
        headerName: 'Contact',
        children: [
            {headerName: "Mobile", field: "mobile", width: 150, filter: 'agTextColumnFilter', sortable: true},
            {headerName: "Land-line", field: "landline", width: 150, filter: 'agTextColumnFilter', sortable: true},
            {headerName: "Address", field: "address", width: 500, filter: 'agTextColumnFilter', sortable: true}
        ]
    }
];

const gridOptions = {
    columnDefs: columnDefs,
    rowData: createRowData(),
    // a callback that gets called whenever the grids data changes
    onModelUpdated: modelUpdated
};

function addDestroyListener() {
    const btDestroyGrid = document.querySelector('#btDestroyGrid');
    btDestroyGrid.addEventListener('click', function () {
        gridOptions.api.destroy();
        btDestroyGrid.disabled = true;
    });
}

function addQuickFilterListener() {
    const eInput = document.querySelector('#quickFilterInput');
    eInput.addEventListener("input", function () {
        const text = eInput.value;
        gridOptions.api.setQuickFilter(text);
    });
}

function addRefreshDataViaApi() {
    const eButton = document.querySelector('#btRefreshDataViaApi');
    eButton.addEventListener("click", function () {
        const data = createRowData();
        gridOptions.api.setRowData(data);
    });
}

function addRefreshDataViaElement() {
    const eButton = document.querySelector('#btRefreshDataViaElement');
    eButton.addEventListener("click", function () {
        const myGrid = document.querySelector('#myGrid');
        myGrid.rowData = createRowData();
    });
}

function modelUpdated() {
    const model = gridOptions.api.getModel();
    const totalRows = gridOptions.rowData.length;
    const processedRows = model.getPageLastRow() - 1;
    const eSpan = document.querySelector('#rowCount');
    eSpan.innerHTML = processedRows.toLocaleString() + ' / ' + totalRows.toLocaleString();
}

function createRowData() {
    const rowData = [];

    for (let i = 0; i < 10000; i++) {
        //for (var i = 0; i < 10000; i++) {
        const countryData = countries[i % countries.length];
        rowData.push({
            name: firstNames[i % firstNames.length] + ' ' + lastNames[i % lastNames.length],
            skills: {
                android: Math.random() < 0.4,
                html5: Math.random() < 0.4,
                mac: Math.random() < 0.4,
                windows: Math.random() < 0.4,
                css: Math.random() < 0.4
            },
            address: addresses[i % addresses.length],
            years: Math.round(Math.random() * 100),
            proficiency: Math.round(Math.random() * 100),
            country: countryData.country,
            continent: countryData.continent,
            language: countryData.language,
            mobile: createRandomPhoneNumber(),
            landline: createRandomPhoneNumber()
        });
    }

    return rowData;
}

function skillsCellRenderer(params) {
    const data = params.data;
    const skills = [];
    IT_SKILLS.forEach(function (skill) {
        if (data && data.skills[skill]) {
            skills.push('<img src="/images/skills/' + skill + '.png" width="16px" title="' + skill + '" />');
        }
    });
    return skills.join(' ');
}

function countryCellRenderer(params) {
    const flag = "<img border='0' width='15' height='10' style='margin-bottom: 2px' src='http://flags.fmcdn.net/data/flags/mini/" + COUNTRY_CODES[params.value] + ".png'>";
    return flag + " " + params.value;
}

function createRandomPhoneNumber() {
    let result = '+';
    for (let i = 0; i < 12; i++) {
        result += Math.round(Math.random() * 10);
        if (i === 2 || i === 5 || i === 8) {
            result += ' ';
        }
    }
    return result;
}

function percentCellRenderer(params) {
    const value = params.value;

    const eDivPercentBar = document.createElement('div');
    eDivPercentBar.className = 'div-percent-bar';
    eDivPercentBar.style.width = value + '%';
    if (value < 20) {
        eDivPercentBar.style.backgroundColor = 'red';
    } else if (value < 60) {
        eDivPercentBar.style.backgroundColor = '#ff9900';
    } else {
        eDivPercentBar.style.backgroundColor = '#00A000';
    }

    const eValue = document.createElement('div');
    eValue.className = 'div-percent-value';
    eValue.innerHTML = value + '%';

    const eOuterDiv = document.createElement('div');
    eOuterDiv.className = 'div-outer-div';
    eOuterDiv.appendChild(eDivPercentBar);
    eOuterDiv.appendChild(eValue);

    return eOuterDiv;
}

const SKILL_TEMPLATE =
    '<label style="border: 1px solid lightgrey; margin: 4px; padding: 4px;">' +
    '  <span>' +
    '    <div style="text-align: center;">SKILL_NAME</div>' +
    '    <div>' +
    '      <input type="checkbox"/>' +
    '      <img src="/images/skills/SKILL.png" width="30px"/>' +
    '    </div>' +
    '  </span>' +
    '</label>';

const FILTER_TITLE =
    '<div style="text-align: center; background: lightgray; width: 100%; display: block; border-bottom: 1px solid grey;">' +
    '<b>TITLE_NAME</b>' +
    '</div>';

function SkillFilter() {
}

SkillFilter.prototype.init = function (params) {
    this.filterChangedCallback = params.filterChangedCallback;
    this.model = {
        android: false,
        css: false,
        html5: false,
        mac: false,
        windows: false
    };
};

SkillFilter.prototype.getModel = function () {

};

SkillFilter.prototype.setModel = function (model) {

};

SkillFilter.prototype.getGui = function () {
    const eGui = document.createElement('div');
    const eInstructions = document.createElement('div');
    eInstructions.innerHTML = FILTER_TITLE.replace('TITLE_NAME', 'Custom Skills Filter');
    eGui.appendChild(eInstructions);

    const that = this;

    IT_SKILLS.forEach(function (skill, index) {
        const skillName = IT_SKILLS_NAMES[index];
        const eSpan = document.createElement('span');
        const html = SKILL_TEMPLATE.replace("SKILL_NAME", skillName).replace("SKILL", skill);
        eSpan.innerHTML = html;

        const eCheckbox = eSpan.querySelector('input');
        eCheckbox.addEventListener('click', function () {
            that.model[skill] = eCheckbox.checked;
            that.filterChangedCallback();
        });

        eGui.appendChild(eSpan);
    });

    return eGui;
};

SkillFilter.prototype.doesFilterPass = function (params) {

    const rowSkills = params.data.skills;
    const model = this.model;
    let passed = true;

    IT_SKILLS.forEach(function (skill) {
        if (model[skill]) {
            if (!rowSkills[skill]) {
                passed = false;
            }
        }
    });

    return passed;
};

SkillFilter.prototype.isFilterActive = function () {
    const model = this.model;
    const somethingSelected = model.android || model.css || model.html5 || model.mac || model.windows;
    return somethingSelected;
};

const PROFICIENCY_TEMPLATE =
    '<label style="padding-left: 4px;">' +
    '<input type="radio" name="RANDOM"/>' +
    'PROFICIENCY_NAME' +
    '</label>';

const PROFICIENCY_NONE = 'none';
const PROFICIENCY_ABOVE40 = 'above40';
const PROFICIENCY_ABOVE60 = 'above60';
const PROFICIENCY_ABOVE80 = 'above80';

const PROFICIENCY_NAMES = ['No Filter', 'Above 40%', 'Above 60%', 'Above 80%'];
const PROFICIENCY_VALUES = [PROFICIENCY_NONE, PROFICIENCY_ABOVE40, PROFICIENCY_ABOVE60, PROFICIENCY_ABOVE80];

function ProficiencyFilter() {
}

ProficiencyFilter.prototype.init = function (params) {
    this.filterChangedCallback = params.filterChangedCallback;
    this.selected = PROFICIENCY_NONE;
    this.valueGetter = params.valueGetter;
};

ProficiencyFilter.prototype.getModel = function () {

};

ProficiencyFilter.prototype.setModel = function (model) {

};

ProficiencyFilter.prototype.getGui = function () {
    const eGui = document.createElement('div');
    const eInstructions = document.createElement('div');
    eInstructions.innerHTML = FILTER_TITLE.replace('TITLE_NAME', 'Custom Proficiency Filter');
    eGui.appendChild(eInstructions);

    const random = '' + Math.random();

    const that = this;
    PROFICIENCY_NAMES.forEach(function (name, index) {
        const eFilter = document.createElement('div');
        const html = PROFICIENCY_TEMPLATE.replace('PROFICIENCY_NAME', name).replace('RANDOM', random);
        eFilter.innerHTML = html;
        const eRadio = eFilter.querySelector('input');
        if (index === 0) {
            eRadio.checked = true;
        }
        eGui.appendChild(eFilter);

        eRadio.addEventListener('click', function () {
            that.selected = PROFICIENCY_VALUES[index];
            that.filterChangedCallback();
        });
    });

    return eGui;
};

ProficiencyFilter.prototype.doesFilterPass = function (params) {

    const value = this.valueGetter(params);
    const valueAsNumber = parseFloat(value);

    switch (this.selected) {
        case PROFICIENCY_ABOVE40 :
            return valueAsNumber >= 40;
        case PROFICIENCY_ABOVE60 :
            return valueAsNumber >= 60;
        case PROFICIENCY_ABOVE80 :
            return valueAsNumber >= 80;
        default :
            return true;
    }

};

ProficiencyFilter.prototype.isFilterActive = function () {
    return this.selected !== PROFICIENCY_NONE;
};

// wait for the document to be loaded, otherwise
// ag-Grid will not find the div in the document.
document.addEventListener("DOMContentLoaded", function () {

    const myGrid = document.querySelector('#myGrid');
    myGrid.gridOptions = gridOptions;

    // add events to grid option 1 - add an event listener
    myGrid.addEventListener('columnresized', function (event) {
        console.log('event via option 1: ' + event.agGridDetails);
    });

    // add events to grid option 2 - callback on the element
    myGrid.oncolumnresized = function (event) {
        console.log('event via option 2: ' + event.agGridDetails);
    };

    // add events to grid option 3 - callback on the grid options
    gridOptions.onColumnResized = function (event) {
        console.log('event via option 3: ' + event);
    };

    addQuickFilterListener();
    addRefreshDataViaApi();
    addRefreshDataViaElement();
    addDestroyListener();
});
