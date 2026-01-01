window.nepaliCalendar = (function ($) {
    "use strict";
    var bsadMap = {
        "N": {
            "2074": {
                '01': { from: "2017-04-14", to: "2017-05-14" },
                '02': { from: "2017-05-15", to: "2017-06-14" },
                '03': { from: "2017-06-15", to: "2017-07-15" },
                '04': { from: "2017-07-16", to: "2017-08-16" },
                '05': { from: "2017-08-17", to: "2017-09-16" },
                '06': { from: "2017-09-17", to: "2017-10-17" },
                '07': { from: "2017-10-18", to: "2017-11-16" },
                '08': { from: "2017-11-17", to: "2017-12-15" },
                '09': { from: "2017-12-16", to: "2018-01-14" },
                '10': { from: "2018-01-15", to: "2018-02-12" },
                '11': { from: "2018-02-13", to: "2018-03-14" },
                '12': { from: "2018-03-15", to: "2018-04-13" }
            },
            "2075": {
                '01': { from: "2018-04-14", to: "2018-05-14" },
                '02': { from: "2018-05-15", to: "2018-06-14" },
                '03': { from: "2018-06-15", to: "2018-07-16" },
                '04': { from: "2018-07-17", to: "2018-08-16" },
                '05': { from: "2018-08-17", to: "2018-09-16" },
                '06': { from: "2018-09-17", to: "2018-10-17" },
                '07': { from: "2018-10-18", to: "2018-11-16" },
                '08': { from: "2018-11-17", to: "2018-12-15" },
                '09': { from: "2018-12-16", to: "2019-01-14" },
                '10': { from: "2019-01-15", to: "2019-02-12" },
                '11': { from: "2019-02-13", to: "2019-03-14" },
                '12': { from: "2019-03-15", to: "2019-04-13" }
            },
            "2076": {
                '01': { from: "2019-04-14", to: "2019-05-14" },
                '02': { from: "2019-05-15", to: "2019-06-15" },
                '03': { from: "2019-06-16", to: "2019-07-16" },
                '04': { from: "2019-07-17", to: "2019-08-17" },
                '05': { from: "2019-08-18", to: "2019-09-17" },
                '06': { from: "2019-09-18", to: "2019-10-17" },
                '07': { from: "2019-10-18", to: "2019-11-16" },
                '08': { from: "2019-11-17", to: "2019-12-16" },
                '09': { from: "2019-12-17", to: "2020-01-14" },
                '10': { from: "2020-01-15", to: "2020-02-12" },
                '11': { from: "2020-02-13", to: "2020-03-13" },
                '12': { from: "2020-03-14", to: "2020-04-12" }
            },
            "2077": {
                '01': { from: "2020-04-13", to: "2020-05-13" },
                '02': { from: "2020-05-14", to: "2020-06-14" },
                '03': { from: "2020-06-15", to: "2020-07-15" },
                '04': { from: "2020-07-16", to: "2020-08-16" },
                '05': { from: "2020-08-17", to: "2020-09-16" },
                '06': { from: "2020-09-17", to: "2020-10-16" },
                '07': { from: "2020-10-17", to: "2020-11-15" },
                '08': { from: "2020-11-16", to: "2020-12-15" },
                '09': { from: "2020-12-16", to: "2021-01-13" },
                '10': { from: "2021-01-14", to: "2021-02-12" },
                '11': { from: "2021-02-13", to: "2021-03-13" },
                '12': { from: "2021-03-14", to: "2021-04-13" }
            },
            "2078": {
                '01': { from: "2021-04-14", to: "2021-05-14" },
                '02': { from: "2021-05-15", to: "2021-06-14" },
                '03': { from: "2021-06-15", to: "2021-07-15" },
                '04': { from: "2021-07-16", to: "2021-08-16" },
                '05': { from: "2021-08-17", to: "2021-09-16" },
                '06': { from: "2021-09-17", to: "2021-10-17" },
                '07': { from: "2021-10-18", to: "2021-11-16" },
                '08': { from: "2021-11-17", to: "2021-12-15" },
                '09': { from: "2021-12-16", to: "2022-01-14" },
                '10': { from: "2022-01-15", to: "2022-02-12" },
                '11': { from: "2022-02-13", to: "2022-03-14" },
                '12': { from: "2022-03-15", to: "2022-04-13" }
            },
            "2079": {
                '01': { from: "2022-04-14", to: "2022-05-14" },
                '02': { from: "2022-05-15", to: "2022-06-14" },
                '03': { from: "2022-06-15", to: "2022-07-16" },
                '04': { from: "2022-07-17", to: "2022-08-16" },
                '05': { from: "2022-08-17", to: "2022-09-16" },
                '06': { from: "2022-09-17", to: "2022-10-17" },
                '07': { from: "2022-10-18", to: "2022-11-16" },
                '08': { from: "2022-11-17", to: "2022-12-15" },
                '09': { from: "2022-12-16", to: "2023-01-14" },
                '10': { from: "2023-01-15", to: "2023-02-12" },
                '11': { from: "2023-02-13", to: "2023-03-14" },
                '12': { from: "2023-03-15", to: "2023-04-13" }
            },

            "2080": {
                '01': { from: "2023-04-14", to: "2023-05-14" },
                '02': { from: "2023-05-15", to: "2023-06-15" },
                '03': { from: "2023-06-16", to: "2023-07-16" },
                '04': { from: "2023-07-17", to: "2023-08-17" },
                '05': { from: "2023-08-18", to: "2023-09-17" },
                '06': { from: "2023-09-18", to: "2023-10-17" },
                '07': { from: "2023-10-18", to: "2023-11-16" },
                '08': { from: "2023-11-17", to: "2023-12-16" },
                '09': { from: "2023-12-17", to: "2024-01-14" },
                '10': { from: "2024-01-15", to: "2024-02-12" },
                '11': { from: "2024-02-13", to: "2024-03-13" },
                '12': { from: "2024-03-14", to: "2024-04-12" }
            },
            "2081": {
                '01': { from: "2024-04-13", to: "2024-05-13" },
                '02': { from: "2024-05-14", to: "2024-06-14" },
                '03': { from: "2024-06-15", to: "2024-07-15" },
                '04': { from: "2024-07-16", to: "2024-08-16" },
                '05': { from: "2024-08-17", to: "2024-09-16" },
                '06': { from: "2024-09-17", to: "2024-10-16" },
                '07': { from: "2024-10-17", to: "2024-11-15" },
                '08': { from: "2024-11-16", to: "2024-12-15" },
                '09': { from: "2024-12-16", to: "2025-01-13" },
                '10': { from: "2025-01-14", to: "2025-02-12" },
                '11': { from: "2025-02-13", to: "2025-03-13" },
                '12': { from: "2025-03-14", to: "2025-04-13" }
            },
            "2082": {
                '01': { from: "2025-04-14", to: "2025-05-14" },
                '02': { from: "2025-05-15", to: "2025-06-14" },
                '03': { from: "2025-06-15", to: "2025-07-16" },
                '04': { from: "2025-07-17", to: "2025-08-16" },
                '05': { from: "2025-08-17", to: "2025-09-16" },
                '06': { from: "2025-09-17", to: "2025-10-17" },
                '07': { from: "2025-10-18", to: "2025-11-16" },
                '08': { from: "2025-11-17", to: "2025-12-15" },
                '09': { from: "2025-12-16", to: "2026-01-14" },
                '10': { from: "2026-01-15", to: "2026-02-12" },
                '11': { from: "2026-02-13", to: "2026-03-14" },
                '12': { from: "2026-03-15", to: "2026-04-13" }
            }
        },
        "E": {
            "2019": {
                "01": { "from": "2019-01-01", "to": "2019-01-31" },
                "02": { "from": "2019-02-01", "to": "2019-02-28" },
                "03": { "from": "2019-03-01", "to": "2019-03-31" },
                "04": { "from": "2019-04-01", "to": "2019-04-30" },
                "05": { "from": "2019-05-01", "to": "2019-05-31" },
                "06": { "from": "2019-06-01", "to": "2019-06-30" },
                "07": { "from": "2019-07-01", "to": "2019-07-31" },
                "08": { "from": "2019-08-01", "to": "2019-08-31" },
                "09": { "from": "2019-09-01", "to": "2019-09-30" },
                "10": { "from": "2019-10-01", "to": "2019-10-31" },
                "11": { "from": "2019-11-01", "to": "2019-11-30" },
                "12": { "from": "2019-12-01", "to": "2019-12-31" }
            },
            "2020": {
                "01": { "from": "2020-01-01", "to": "2020-01-31" },
                "02": { "from": "2020-02-01", "to": "2020-02-29" },
                "03": { "from": "2020-03-01", "to": "2020-03-31" },
                "04": { "from": "2020-04-01", "to": "2020-04-30" },
                "05": { "from": "2020-05-01", "to": "2020-05-31" },
                "06": { "from": "2020-06-01", "to": "2020-06-30" },
                "07": { "from": "2020-07-01", "to": "2020-07-31" },
                "08": { "from": "2020-08-01", "to": "2020-08-31" },
                "09": { "from": "2020-09-01", "to": "2020-09-30" },
                "10": { "from": "2020-10-01", "to": "2020-10-31" },
                "11": { "from": "2020-11-01", "to": "2020-11-30" },
                "12": { "from": "2020-12-01", "to": "2020-12-31" }
            },
            "2021": {
                "01": { "from": "2021-01-01", "to": "2021-01-31" },
                "02": { "from": "2021-02-01", "to": "2021-02-28" },
                "03": { "from": "2021-03-01", "to": "2021-03-31" },
                "04": { "from": "2021-04-01", "to": "2021-04-30" },
                "05": { "from": "2021-05-01", "to": "2021-05-31" },
                "06": { "from": "2021-06-01", "to": "2021-06-30" },
                "07": { "from": "2021-07-01", "to": "2021-07-31" },
                "08": { "from": "2021-08-01", "to": "2021-08-31" },
                "09": { "from": "2021-09-01", "to": "2021-09-30" },
                "10": { "from": "2021-10-01", "to": "2021-10-31" },
                "11": { "from": "2021-11-01", "to": "2021-11-30" },
                "12": { "from": "2021-12-01", "to": "2021-12-31" }
            },
            "2022": {
                "01": { "from": "2022-01-01", "to": "2022-01-31" },
                "02": { "from": "2022-02-01", "to": "2022-02-28" },
                "03": { "from": "2022-03-01", "to": "2022-03-31" },
                "04": { "from": "2022-04-01", "to": "2022-04-30" },
                "05": { "from": "2022-05-01", "to": "2022-05-31" },
                "06": { "from": "2022-06-01", "to": "2022-06-30" },
                "07": { "from": "2022-07-01", "to": "2022-07-31" },
                "08": { "from": "2022-08-01", "to": "2022-08-31" },
                "09": { "from": "2022-09-01", "to": "2022-09-30" },
                "10": { "from": "2022-10-01", "to": "2022-10-31" },
                "11": { "from": "2022-11-01", "to": "2022-11-30" },
                "12": { "from": "2022-12-01", "to": "2022-12-31" }
            },
            "2023": {
                "01": { "from": "2023-01-01", "to": "2023-01-31" },
                "02": { "from": "2023-02-01", "to": "2023-02-28" },
                "03": { "from": "2023-03-01", "to": "2023-03-31" },
                "04": { "from": "2023-04-01", "to": "2023-04-30" },
                "05": { "from": "2023-05-01", "to": "2023-05-31" },
                "06": { "from": "2023-06-01", "to": "2023-06-30" },
                "07": { "from": "2023-07-01", "to": "2023-07-31" },
                "08": { "from": "2023-08-01", "to": "2023-08-31" },
                "09": { "from": "2023-09-01", "to": "2023-09-30" },
                "10": { "from": "2023-10-01", "to": "2023-10-31" },
                "11": { "from": "2023-11-01", "to": "2023-11-30" },
                "12": { "from": "2023-12-01", "to": "2023-12-31" }
            },
            "2024": {
                "01": { "from": "2024-01-01", "to": "2024-01-31" },
                "02": { "from": "2024-02-01", "to": "2024-02-29" },
                "03": { "from": "2024-03-01", "to": "2024-03-31" },
                "04": { "from": "2024-04-01", "to": "2024-04-30" },
                "05": { "from": "2024-05-01", "to": "2024-05-31" },
                "06": { "from": "2024-06-01", "to": "2024-06-30" },
                "07": { "from": "2024-07-01", "to": "2024-07-31" },
                "08": { "from": "2024-08-01", "to": "2024-08-31" },
                "09": { "from": "2024-09-01", "to": "2024-09-30" },
                "10": { "from": "2024-10-01", "to": "2024-10-31" },
                "11": { "from": "2024-11-01", "to": "2024-11-30" },
                "12": { "from": "2024-12-01", "to": "2024-12-31" }
            },
            "2025": {
                "01": { "from": "2025-01-01", "to": "2025-01-31" },
                "02": { "from": "2025-02-01", "to": "2025-02-28" },
                "03": { "from": "2025-03-01", "to": "2025-03-31" },
                "04": { "from": "2025-04-01", "to": "2025-04-30" },
                "05": { "from": "2025-05-01", "to": "2025-05-31" },
                "06": { "from": "2025-06-01", "to": "2025-06-30" },
                "07": { "from": "2025-07-01", "to": "2025-07-31" },
                "08": { "from": "2025-08-01", "to": "2025-08-31" },
                "09": { "from": "2025-09-01", "to": "2025-09-30" },
                "10": { "from": "2025-10-01", "to": "2025-10-31" },
                "11": { "from": "2025-11-01", "to": "2025-11-30" },
                "12": { "from": "2025-12-01", "to": "2025-12-31" }
            }
        }

    };
    var weekdaytemplate = {
        1: [],
        2: [],
        3: [],
        4: [],
        5: [],
        6: [],
        7: []
    };

    var weekday = null;
    var months = {
        "N": {
            '01': 'बैशाख',
            '02': 'जेष्ठ',
            '03': 'असार',
            '04': 'श्रावण',
            '05': 'भाद्र',
            '06': 'आश्विन',
            '07': 'कार्तिक',
            '08': 'मंसिर',
            '09': 'पुष',
            '10': 'माघ',
            '11': 'फाल्गुन',
            '12': 'चैत्र',
        },
        "E": {
            '01': 'January',
            '02': 'February',
            '03': 'March',
            '04': 'April',
            '05': 'May',
            '06': 'June',
            '07': 'July',
            '08': 'August',
            '09': 'September',
            '10': 'October',
            '11': 'November',
            '12': 'December',
        }

    }
    var formatDate = function (date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        return [year, month, day].join('-');
    }
    console.log(document.calendarType);

    var getYears = function () {
        return (document.calendarType == 'E') ? Object.keys(bsadMap.E) : Object.keys(bsadMap.N);
    };
    var getMonths = function () {
        return (document.calendarType == 'E') ? months.E : months.N;
    };

    var getCalendar = function (year, month) {
        var bsadMapType = (document.calendarType == 'E') ? bsadMap.E : bsadMap.N;
        var weekday = $.extend(true, {}, weekdaytemplate);
        var monthData = bsadMapType[year][month];
        var fromDate = new Date(monthData['from']);
        var toDate = new Date(monthData['to']);
        var timeDiff = Math.abs(toDate.getTime() - fromDate.getTime()) + 3600;
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
        var f = false;
        var j = 0;
        var loopDate = new Date();
        for (var i = 0; i < 50; i++) {
            var index = i % 7;
            if (!f) {
                if (index === fromDate.getDay()) {
                    f = true;
                } else {
                    weekday[index + 1].push({});
                }
            }

            if (f) {
                if (j == diffDays) {
                    break;
                }
                loopDate.setTime(fromDate.getTime() + j * 86400000);
                weekday[index + 1].push({ date: formatDate(loopDate), day: j + 1 });
                j++;
            }

        }
        return weekday;
    };
    return {
        getCalendar: getCalendar,
        bsadMap: bsadMap,
        getYears: getYears,
        getMonths: getMonths
    };
})(window.jQuery);
(function ($, app, nc) {
    'use strict';
    $(document).ready(function () {
        var $nepaliCalendar = $('#nepaliCalendar');
        var $content = $('#nc-content');
        var $sunday = $content.find('#nc-sunday');
        var $monday = $content.find('#nc-monday');
        var $tuesday = $content.find('#nc-tuesday');
        var $wednesday = $content.find('#nc-wednesday');
        var $thrusday = $content.find('#nc-thrusday');
        var $friday = $content.find('#nc-friday');
        var $saturday = $content.find('#nc-saturday');

        var switchInput = document.getElementById('calendar-switch');

        switchInput.addEventListener('change', function () {
            var calenderLg = this.checked ? 'E' : 'N';

            app.serverRequest(document.changeCalendar, { data: null, calenderLg: calenderLg }).then(function () {
                app.showMessage('Operation successful', 'success');
                location.reload();
            }, function (error) {
                console.log(error);
            });

        });

        var template = `
        <div class='nc-date'>
            <table class="table table-condensed" style="inherit">
                <tr>
                    <td colspan="2" class="day" style="font-size:12px;">
                    <td>
                </tr>
                <tr>
                    <td colspan="2" class="status" style="font-size:0.8em;">
                    <td>
                </tr>
                <tr>
                    <td class="in-time" style="font-size:12px;"></td>
                    <td class="out-time" style="font-size:12px;"></td>
                </tr>
            </table>
        </div>`;


        var $year = $('#nc-year');
        var $month = $('#nc-month');

        var years = nc.getYears();
        var months = nc.getMonths();

        $month.on('change', function () {
            loadCalendar($year.val(), $month.val());
        });

        // $('#cal_emp').on('change', function() {
        //     let selectedEmployeeId = $(this).val();
        //     let selectedProfileVal = document.empProfile[selectedEmployeeId];
        //     if (selectedProfileVal != null) {
        //         $('#employeeImageCalendar').attr("src", document.basePath + '/uploads/' + selectedProfileVal);
        //     } else {
        //         $('#employeeImageCalendar').attr("src", document.basePath + '/img/nobody_m.original.jpg');
        //     }
        //     loadCalendar($year.val(), $month.val());
        // });

        $('#cal_emp').on('change', function () {
            let selectedEmployeeId = $(this).val();  // Get selected ID first
            let selectedProfile = document.empProfile[selectedEmployeeId];  // Get the profile

            // console.log(selectedProfile);  // Debug output

            let selectedProfileVal = selectedProfile['FILE_PATH'];  // Use string key

            if (selectedProfileVal != null) {
                $('#employeeImageCalendar').attr("src", document.basePath + '/uploads/' + selectedProfileVal);
            } else {
                $('#employeeImageCalendar').attr("src", document.basePath + '/img/nobody_m.original.jpg');
            }

            let joinDate = selectedProfile['JOIN_DATE'];
            if (joinDate != null) {
                let joinDateObj = new Date(joinDate);

                // Format join date: 16-NOV-21
                const monthAbbr = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN",
                    "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
                let day = String(joinDateObj.getDate()).padStart(2, '0');
                let month = monthAbbr[joinDateObj.getMonth()];
                let year = String(joinDateObj.getFullYear()).slice(-2);
                let formattedJoinDate = `${day}-${month}-${year}`;

                $('#joinDate').text(`Join Date: ${formattedJoinDate}`);

                // Calculate service duration
                let currentDate = new Date();
                let years = currentDate.getFullYear() - joinDateObj.getFullYear();
                let months = currentDate.getMonth() - joinDateObj.getMonth();
                let days = currentDate.getDate() - joinDateObj.getDate();

                if (days < 0) {
                    months--;
                    let prevMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
                    days += prevMonth.getDate();
                }

                if (months < 0) {
                    years--;
                    months += 12;
                }

                let serviceText = `At work for: ${years} year${years !== 1 ? 's' : ''} ${months} month${months !== 1 ? 's' : ''} ${days} day${days !== 1 ? 's' : ''}`;
                $('#serviceYears').text(serviceText);
            } else {
                $('#joinDate').text('Join Date: N/A');
                $('#serviceYears').text('N/A');
            }



            // Set OFFICIAL EMAIL 
            let employeeOfficialEmail = selectedProfile['EMAIL_OFFICIAL'];
            if (employeeOfficialEmail != null) {
                $('#employeeOfficialEmail').text(employeeOfficialEmail);
            } else {
                $('#employeeOfficialEmail').text('N/A'); // fallback if EMAIL_OFFICIAL is missing
            }

            // Set full name 
            let employeeFullName = selectedProfile['FULL_NAME'];
            if (employeeFullName != null) {
                $('#employeeFullName').text(employeeFullName);
            } else {
                $('#employeeFullName').text('N/A');
            }

            // Set DESIGNATION_TITLE
            let employeeDesignationTitle = selectedProfile['DESIGNATION_TITLE'];
            if (employeeDesignationTitle != null) {
                $('#employeeDesignationTitle').text(employeeDesignationTitle);
            } else {
                $('#employeeDesignationTitle').text('N/A'); // fallback if EMAIL_OFFICIAL is missing
            }

            loadCalendar($year.val(), $month.val());
        });

        var serverDate = (document.calendarType == 'E') ? 'getServerDateForCalender' : 'getServerDateBS';

        app.pullDataById(document.restfulUrl, { action: serverDate }).then(function (response) {
            var currentDate = (document.calendarType == 'E') ? response.data.serverDate : response.data.CURRENT_DATE;
            var currentYear = currentDate.split('-')[0];
            var currentMonth = currentDate.split('-')[1];
            $year.html('');
            for (var i in years) {
                if (years[i] == currentYear) {
                    $year.append($("<option selected='selected'></option>").val(years[i]).text(years[i]));
                } else {
                    $year.append($("<option></option>").val(years[i]).text(years[i]));
                }
            }
            $month.html('');
            for (var i in months) {
                if (i == currentMonth) {
                    $month.append($("<option selected='selected'></option>").val(i).text(months[i]));
                } else {
                    $month.append($("<option></option>").val(i).text(months[i]));
                }
            }
            loadCalendar(currentYear, currentMonth);
        });

        function convertToNepaliNumber(num) {
            const englishToNepali = {
                '0': '०',
                '1': '१',
                '2': '२',
                '3': '३',
                '4': '४',
                '5': '५',
                '6': '६',
                '7': '७',
                '8': '८',
                '9': '९'
            };

            if (num == null) {
                return " - ";
            }

            return num.toString().split('').map(digit => englishToNepali[digit] || digit).join('');
        }

        var loadCalendar = function (year, month) {

            var monthData = nc.getCalendar(year, month);
            $sunday.html('');
            $monday.html('');
            $tuesday.html('');
            $wednesday.html('');
            $thrusday.html('');
            $friday.html('');
            $saturday.html('');

            function appendDayData(dayElement, dayIndex, data) {
                var dayData = monthData[dayIndex];
                for (var i = 0; i < dayData.length; i++) {
                    var $template = $(template);
                    $template.attr('date', dayData[i].date || " - ");
                    var dayValue = dayData[i].day;
                    var nepaliDay = (document.calendarType == 'E') ? dayData[i].day : convertToNepaliNumber(dayValue);
                    $template.find('.day').append(nepaliDay || " - ");
                    $(dayElement).append($template);
                }
            }

            appendDayData($sunday, 1);
            appendDayData($monday, 2);
            appendDayData($tuesday, 3);
            appendDayData($wednesday, 4);
            appendDayData($thrusday, 5);
            appendDayData($friday, 6);
            appendDayData($saturday, 7);

            var m = (document.calendarType == 'E') ? nc.bsadMap['E'][year][month] : nc.bsadMap['N'][year][month];

            var selEmp = $('#cal_emp').val();

            function changeStatus($status) {
                switch ($status) {
                    case 'Absent':
                        return 'अनुपस्थित';
                    case 'Day Off':
                        return 'बिदा';
                    case 'Present':
                        return 'उपस्थित';
                    default:
                        return $status;
                }
            }

            app.pullDataById(document.calendarJsonFeedUrl, { 'startDate': m.from, 'endDate': m.to, 'selEmp': selEmp }).then(function (response) {
                $.each(response, function (key, value) {
                    var $date = $nepaliCalendar.find('[date=' + value.ATTENDANCE_DT + ']');
                    $date.find('.in-time').html(value.IN_TIME);
                    $date.find('.out-time').html(value.OUT_TIME);
                    $date.find('.status').html((document.calendarType == 'N') ? changeStatus(value.ATTENDANCE_STATUS) : value.ATTENDANCE_STATUS);
                    console.log(value.ATTENDANCE_STATUS);

                    if (value.OVERALL_STATUS == 'DO' || value.OVERALL_STATUS == 'WD') {
                        $date.css('background-color', '#ADFF2F');
                        $date.children().css('background-color', '#ADFF2F');
                    } else if (value.OVERALL_STATUS == 'HD' || value.OVERALL_STATUS == 'WH') {
                        $date.css('background-color', '#eaea2a');
                        $date.children().css('background-color', '#eaea2a');
                    } else if (value.OVERALL_STATUS == 'LV' || value.OVERALL_STATUS == 'LP') {
                        $date.css('background-color', '#a7aeaf');
                        $date.children().css('background-color', '#a7aeaf');
                    } else if (value.OVERALL_STATUS == 'TN' || value.OVERALL_STATUS == 'TP') {
                        $date.css('background-color', '#39c7b8');
                        $date.children().css('background-color', '#39c7b8');
                    } else if (value.OVERALL_STATUS == 'TV' || value.OVERALL_STATUS == 'VP') {
                        $date.css('background-color', '#e89c0a');
                        $date.children().css('background-color', '#e89c0a');
                        $date.children().css('color', '#FFFFFF');
                    } else if (value.OVERALL_STATUS == 'AB') {
                        $date.css('background-color', '#cc0000');
                        $date.children().css('background-color', '#cc0000');
                        $date.children().css('color', '#FFFFFF');
                    } else if (value.OVERALL_STATUS == 'EC') {
                        $date.css('background-color', '#d14ef5');
                        $date.children().css('background-color', '#d14ef5');
                    }
                });

            },
                function (error) {
                    // Handle error if needed
                });

        };
    });
})(window.jQuery, window.app, window.nepaliCalendar);