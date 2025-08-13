/**
 * Admin Analytics
 */
"use strict";

document.addEventListener("DOMContentLoaded", function (e) {
   // Define vibrant color palette
   const colors = {
      primary: "#FF5733", // Vivid Orange
      secondary: "#33FF57", // Lime Green
      info: "#3399FF", // Bright Blue
      success: "#FF33A1", // Hot Pink
   };

   // Current filter states
   let currentFilters = {
      revenuePeriod: "last30days",
      reportPeriod: "last12months",
      expensePeriod: "alltime",
      orderPeriod: "thismonth",
      growthYear: new Date().getFullYear(),
   };

   // Function to get theme-dependent colors
   function getThemeColors() {
      const isDarkMode =
         document.documentElement.getAttribute("data-bs-theme") === "dark";
      return {
         cardColor: isDarkMode ? "#2D2D2D" : "#F8F9FA",
         headingColor: isDarkMode ? "#E0E0E0" : "#333333",
         legendColor: isDarkMode ? "#E0E0E0" : "#333333",
         labelColor: isDarkMode ? "#E0E0E0" : "#333333",
         borderColor: isDarkMode ? "#555555" : "#CCCCCC",
      };
   }

   // Store chart instances for re-rendering
   let chartInstances = {
      orderAreaChart: null,
      lineAreaChart: null,
      totalRevenueChart: null,
      growthChart: null,
      donutChart: null,
   };

   // Function to update dropdown button text
   function updateDropdownText(dropdownButton, text) {
      if (dropdownButton) {
         const textSpan = dropdownButton.querySelector("span");
         if (textSpan) {
            textSpan.textContent = text;
         } else {
            // If no span, add text after icon
            const icon = dropdownButton.querySelector("i");
            if (icon && icon.nextSibling) {
               icon.nextSibling.textContent = " " + text;
            }
         }
      }
   }

   // Function to setup dropdown handlers
   function setupDropdownHandlers() {
      // Revenue period dropdown handlers
      document.querySelectorAll("[data-revenue-period]").forEach((item) => {
         item.addEventListener("click", function (e) {
            e.preventDefault();
            const period = this.getAttribute("data-revenue-period");
            const text = this.textContent.trim();

            currentFilters.revenuePeriod = period;

            // Update dropdown button text
            const dropdownButton =
               this.closest(".dropdown").querySelector(".dropdown-toggle");
            updateDropdownText(dropdownButton, text);

            // Re-render charts with new filter
            renderAnalyticsCharts();
         });
      });

      // Report period dropdown handlers
      document.querySelectorAll("[data-report-period]").forEach((item) => {
         item.addEventListener("click", function (e) {
            e.preventDefault();
            const period = this.getAttribute("data-report-period");
            const text = this.textContent.trim();

            currentFilters.reportPeriod = period;

            // Update dropdown button text
            const dropdownButton =
               this.closest(".dropdown").querySelector(".dropdown-toggle");
            updateDropdownText(dropdownButton, text);

            // Re-render charts with new filter
            renderAnalyticsCharts();
         });
      });

      // Expense period dropdown handlers
      document.querySelectorAll("[data-expense-period]").forEach((item) => {
         item.addEventListener("click", function (e) {
            e.preventDefault();
            const period = this.getAttribute("data-expense-period");
            const text = this.textContent.trim();

            currentFilters.expensePeriod = period;

            // Update dropdown button text
            const dropdownButton =
               this.closest(".dropdown").querySelector(".dropdown-toggle");
            updateDropdownText(dropdownButton, text);

            // Re-render charts with new filter
            renderAnalyticsCharts();
         });
      });

      // Order period dropdown handlers
      document.querySelectorAll("[data-order-period]").forEach((item) => {
         item.addEventListener("click", function (e) {
            e.preventDefault();
            const period = this.getAttribute("data-order-period");
            const text = this.textContent.trim();

            currentFilters.orderPeriod = period;

            // Update dropdown button text
            const dropdownButton =
               this.closest(".dropdown").querySelector(".dropdown-toggle");
            updateDropdownText(dropdownButton, text);

            // Re-render charts with new filter
            renderAnalyticsCharts();
         });
      });

      // Growth year dropdown handlers
      document.querySelectorAll("[data-growth-year]").forEach((item) => {
         item.addEventListener("click", function (e) {
            e.preventDefault();
            const year = this.getAttribute("data-growth-year");
            const text = this.textContent.trim();

            currentFilters.growthYear = year;

            // Update button text
            const currentYearBtn = document.getElementById("currentYearBtn");
            if (currentYearBtn) {
               currentYearBtn.textContent = year;
            }

            // Re-render charts with new filter
            renderAnalyticsCharts();
         });
      });
   }

   // Function to render all analytics charts
   function renderAnalyticsCharts() {
      const { cardColor, headingColor, legendColor, labelColor, borderColor } =
         getThemeColors();
      const fontFamily =
         "Public Sans, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, sans-serif";

      // Show loading indicator
      const loadingElements = document.querySelectorAll(".chart-loading");
      loadingElements.forEach((el) => (el.style.display = "block"));

      // Build query parameters
      const params = new URLSearchParams({
         revenue_period: currentFilters.revenuePeriod,
         report_period: currentFilters.reportPeriod,
         expense_period: currentFilters.expensePeriod,
         order_period: currentFilters.orderPeriod,
         growth_year: currentFilters.growthYear,
      });

      // Fetch dynamic data from server
      fetch(`/admin/analytics-data?${params.toString()}`)
         .then((response) => response.json())
         .then((json) => {
            // Hide loading indicators
            loadingElements.forEach((el) => (el.style.display = "none"));

            if (!json.success) {
               console.error("Failed to fetch analytics data:", json.error);
               return;
            }
            const data = json.data;

            // Destroy existing chart instances to prevent memory leaks
            Object.values(chartInstances).forEach((chart) => {
               if (chart) chart.destroy();
            });

            // Monthly Growth Chart
            const monthlyGrowthChartEl = document.querySelector("#monthlyGrowthChart");
            if (monthlyGrowthChartEl) {
                const monthlyGrowthChartConfig = {
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: { show: false },
                    },
                    series: [{
                        name: 'Bookings',
                        data: data.order_data
                    }],
                    colors: [colors.success],
                    dataLabels: { enabled: false },
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3,
                            stops: [0, 90, 100]
                        }
                    },
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 3,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        padding: {
                            top: -10,
                            right: 25,
                            left: 15
                        }
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeFormatter: {
                                year: 'yyyy',
                                month: "MMM 'yy",
                                day: 'dd MMM',
                            },
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (value) {
                                return value.toFixed(0);
                            },
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            },
                            offsetX: -10
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd MMM yyyy'
                        },
                        y: {
                            formatter: function (val) {
                                return val + " bookings";
                            }
                        }
                    }
                };
                chartInstances.monthlyGrowthChart = new ApexCharts(
                    monthlyGrowthChartEl,
                    monthlyGrowthChartConfig
                );
                chartInstances.monthlyGrowthChart.render();
            }

            // Line Area Chart for Revenue with Dynamic Period
            const lineAreaChartEl = document.querySelector("#lineAreaChart");
            if (lineAreaChartEl) {
               const lineAreaChartConfig = {
                  chart: {
                     height: 300,
                     type: "area",
                     toolbar: { show: false },
                  },
                  dataLabels: { enabled: false },
                  stroke: { curve: "smooth", width: 2 },
                  series: [
                     { name: "Revenue", data: data.revenue_last_30_days },
                  ],
                  xaxis: {
                     type: "datetime",
                     labels: {
                        datetimeFormatter: {
                           year: "yyyy",
                           month: "MMM 'yy",
                           day: "dd MMM",
                           hour: "HH:mm",
                        },
                        style: { colors: labelColor, fontSize: "13px" },
                     },
                     axisBorder: { show: false },
                     axisTicks: { show: false },
                  },
                  yaxis: {
                     labels: {
                        formatter: function (value) {
                           return "GH₵ " + value.toFixed(2);
                        },
                        style: { colors: labelColor, fontSize: "13px" },
                     },
                  },
                  grid: {
                     borderColor: borderColor,
                     strokeDashArray: 3,
                     xaxis: { lines: { show: true } },
                  },
                  colors: [colors.info],
                  fill: {
                     type: "gradient",
                     gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 100],
                     },
                  },
                  tooltip: { x: { format: "dd/MM/yy HH:mm" } },
               };
               chartInstances.lineAreaChart = new ApexCharts(
                  lineAreaChartEl,
                  lineAreaChartConfig
               );
               chartInstances.lineAreaChart.render();
            }

            // Total Revenue Report Chart - Bar Chart with Dynamic Period
            const totalRevenueChartEl =
               document.querySelector("#totalRevenueChart");
            if (totalRevenueChartEl && data.revenue_report) {
               const totalRevenueChartOptions = {
                  series: [
                     {
                        name: "Previous Period",
                        data: data.revenue_report.previous,
                     },
                     {
                        name: "Current Period",
                        data: data.revenue_report.current,
                     },
                  ],
                  chart: {
                     height: 300,
                     stacked: true,
                     type: "bar",
                     toolbar: { show: false },
                  },
                  plotOptions: {
                     bar: {
                        horizontal: false,
                        columnWidth: "33%",
                        borderRadius: 12,
                        startingShape: "rounded",
                        endingShape: "rounded",
                     },
                  },
                  colors: [colors.primary, colors.info],
                  dataLabels: { enabled: false },
                  stroke: { width: 0, colors: [colors.primary, colors.info] },
                  legend: {
                     show: true,
                     horizontalAlign: "left",
                     position: "top",
                     markers: { height: 8, width: 8, radius: 12, offsetX: -3 },
                     labels: { colors: legendColor },
                     itemMargin: { horizontal: 10 },
                  },
                  grid: {
                     borderColor: borderColor,
                     padding: { top: 0, bottom: -8, left: 20, right: 20 },
                  },
                  xaxis: {
                     categories: data.revenue_report.labels,
                     labels: {
                        style: { colors: labelColor, fontSize: "13px" },
                     },
                     axisTicks: { show: false },
                     axisBorder: { show: false },
                  },
                  yaxis: {
                     labels: {
                        style: { colors: labelColor, fontSize: "13px" },
                        formatter: function (value) {
                           return "GH₵ " + value.toFixed(0);
                        },
                     },
                  },
                  states: { hover: { filter: { type: "none" } } },
                  responsive: [
                     {
                        breakpoint: 1700,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 10, columnWidth: "32%" },
                           },
                        },
                     },
                     {
                        breakpoint: 1580,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "35%" },
                           },
                        },
                     },
                     {
                        breakpoint: 1440,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "42%" },
                           },
                        },
                     },
                     {
                        breakpoint: 1300,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "48%" },
                           },
                        },
                     },
                     {
                        breakpoint: 1200,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "40%" },
                           },
                        },
                     },
                     {
                        breakpoint: 1040,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "45%" },
                           },
                        },
                     },
                     {
                        breakpoint: 991,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "38%" },
                           },
                        },
                     },
                     {
                        breakpoint: 840,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "35%" },
                           },
                        },
                     },
                     {
                        breakpoint: 768,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "28%" },
                           },
                        },
                     },
                     {
                        breakpoint: 640,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "32%" },
                           },
                        },
                     },
                     {
                        breakpoint: 576,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "37%" },
                           },
                        },
                     },
                     {
                        breakpoint: 360,
                        options: {
                           plotOptions: {
                              bar: { borderRadius: 8, columnWidth: "70%" },
                           },
                        },
                     },
                  ],
               };
               chartInstances.totalRevenueChart = new ApexCharts(
                  totalRevenueChartEl,
                  totalRevenueChartOptions
               );
               chartInstances.totalRevenueChart.render();
            }

            // Growth Chart - Radial Bar Chart (FIXED)
            const growthChartEl = document.querySelector("#growthChart");
            if (growthChartEl) {
               const growthPercentage = data.company_growth || 0;
               const growthChartOptions = {
                  series: [Math.min(Math.abs(growthPercentage), 100)], // Ensure value is between 0-100
                  labels: ["Growth"],
                  chart: {
                     height: 240,
                     type: "radialBar",
                     sparkline: { enabled: false },
                  },
                  plotOptions: {
                     radialBar: {
                        size: 150,
                        offsetY: 10,
                        startAngle: -150,
                        endAngle: 150,
                        hollow: { size: "55%" },
                        track: {
                           background: cardColor,
                           strokeWidth: "100%",
                        },
                        dataLabels: {
                           name: {
                              offsetY: 15,
                              color: labelColor,
                              fontSize: "15px",
                              fontWeight: "500",
                              fontFamily: fontFamily,
                           },
                           value: {
                              offsetY: -20,
                              color: headingColor,
                              fontSize: "22px",
                              fontWeight: "500",
                              fontFamily: fontFamily,
                              formatter: function (val) {
                                 return (
                                    (growthPercentage >= 0 ? "+" : "") +
                                    growthPercentage +
                                    "%"
                                 );
                              },
                           },
                        },
                     },
                  },
                  colors: [
                     growthPercentage >= 0 ? colors.success : colors.primary,
                  ],
                  fill: {
                     type: "gradient",
                     gradient: {
                        shade: "dark",
                        shadeIntensity: 0.5,
                        gradientToColors: [
                           growthPercentage >= 0
                              ? colors.success
                              : colors.primary,
                        ],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 0.6,
                        stops: [30, 70, 100],
                     },
                  },
                  stroke: { dashArray: 5 },
                  grid: { padding: { top: -35, bottom: -10 } },
                  states: {
                     hover: { filter: { type: "none" } },
                     active: { filter: { type: "none" } },
                  },
               };
               chartInstances.growthChart = new ApexCharts(
                  growthChartEl,
                  growthChartOptions
               );
               chartInstances.growthChart.render();

               // Update revenue summary (FIXED)
               const currentYearRevenue =
                  document.getElementById("currentYearRevenue");
               const previousYearRevenue = document.getElementById(
                  "previousYearRevenue"
               );
               const growthText = document.getElementById("growthText");
               const currentYearLabel =
                  document.getElementById("currentYearLabel");
               const previousYearLabel =
                  document.getElementById("previousYearLabel");

               if (data.growth_data) {
                  if (currentYearRevenue) {
                     currentYearRevenue.textContent = `GH₵${data.growth_data.current_revenue.toFixed(
                        0
                     )}`;
                  }
                  if (previousYearRevenue) {
                     previousYearRevenue.textContent = `GH₵${data.growth_data.previous_revenue.toFixed(
                        0
                     )}`;
                  }
                  if (currentYearLabel) {
                     currentYearLabel.textContent =
                        data.growth_data.current_year;
                  }
                  if (previousYearLabel) {
                     previousYearLabel.textContent =
                        data.growth_data.previous_year;
                  }
               }

               if (growthText) {
                  growthText.textContent = `${
                     growthPercentage >= 0 ? "+" : ""
                  }${growthPercentage}% Company Growth`;
               }
            }

            // Donut Chart for Expense Ratio
            const donutChartEl = document.querySelector("#donutChart");
            if (donutChartEl) {
               const expenseData = data.expense_ratio || {};
               const donutChartConfig = {
                  chart: { height: 390, type: "donut" },
                  labels: Object.keys(expenseData),
                  series: Object.values(expenseData),
                  colors: [
                     colors.primary,
                     colors.secondary,
                     colors.info,
                     colors.success,
                  ],
                  stroke: { show: false, curve: "straight" },
                  dataLabels: {
                     enabled: true,
                     formatter: function (val) {
                        return parseInt(val, 10) + "%";
                     },
                  },
                  legend: {
                     show: true,
                     position: "bottom",
                     markers: { offsetX: -3 },
                     itemMargin: { vertical: 3, horizontal: 10 },
                     labels: { colors: legendColor, useSeriesColors: false },
                  },
                  plotOptions: {
                     pie: {
                        donut: {
                           labels: {
                              show: true,
                              name: {
                                 fontSize: "2rem",
                                 fontFamily: fontFamily,
                              },
                              value: {
                                 fontSize: "1.2rem",
                                 color: legendColor,
                                 fontFamily: fontFamily,
                                 formatter: function (val) {
                                    return parseInt(val, 10) + "%";
                                 },
                              },
                              total: {
                                 show: true,
                                 fontSize: "1.5rem",
                                 color: headingColor,
                                 label: "Expenses",
                                 formatter: function () {
                                    return "100%";
                                 },
                              },
                           },
                        },
                     },
                  },
                  responsive: [
                     {
                        breakpoint: 992,
                        options: {
                           chart: { height: 380 },
                           legend: {
                              position: "bottom",
                              labels: {
                                 colors: legendColor,
                                 useSeriesColors: false,
                              },
                           },
                        },
                     },
                     {
                        breakpoint: 576,
                        options: {
                           chart: { height: 320 },
                           plotOptions: {
                              pie: {
                                 donut: {
                                    labels: {
                                       show: true,
                                       name: { fontSize: "1.5rem" },
                                       value: { fontSize: "1rem" },
                                       total: { fontSize: "1.5rem" },
                                    },
                                 },
                              },
                           },
                           legend: {
                              position: "bottom",
                              labels: {
                                 colors: legendColor,
                                 useSeriesColors: false,
                              },
                           },
                        },
                     },
                     {
                        breakpoint: 420,
                        options: {
                           chart: { height: 280 },
                           legend: { show: false },
                        },
                     },
                     {
                        breakpoint: 360,
                        options: {
                           chart: { height: 250 },
                           legend: { show: false },
                        },
                     },
                  ],
               };
               chartInstances.donutChart = new ApexCharts(
                  donutChartEl,
                  donutChartConfig
               );
               chartInstances.donutChart.render();
            }
         })
         .catch((error) => {
            // Hide loading indicators
            loadingElements.forEach((el) => (el.style.display = "none"));
            console.error("Error fetching analytics data:", error);
         });
   }

   // Initial setup
   setupDropdownHandlers();
   renderAnalyticsCharts();

   // Listen for theme changes
   const htmlElement = document.documentElement;
   const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
         if (mutation.attributeName === "data-bs-theme") {
            renderAnalyticsCharts();
         }
      });
   });
   observer.observe(htmlElement, { attributes: true });
});
