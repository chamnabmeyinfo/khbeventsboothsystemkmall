# CEO-Approved Plan — Single Source of Truth

**All development scope and priorities must follow this plan.** No features or work outside this plan unless formally agreed as a change request with the CEO.

---

## Authoritative documents

The signed, CEO-approved plan is:

| Document                                      | Location                                                                                          |
| --------------------------------------------- | ------------------------------------------------------------------------------------------------- |
| **System Development Detail Plan**            | `chamnabmey_documents/CEO_Meeting_Package/Approved Documents/System Development Detail Plan.pdf`  |
| **System Development Detail Plan** (editable) | `chamnabmey_documents/CEO_Meeting_Package/Approved Documents/System Development Detail Plan.docx` |
| **Service Agreement**                         | `chamnabmey_documents/CEO_Meeting_Package/Approved Documents/Service_Agreement_KHB_Events.pdf`    |

When in doubt, the PDF in **Approved Documents** is the source of truth.

---

## Plan period and calendar

| Item            | Value                                                           |
| --------------- | --------------------------------------------------------------- |
| **Plan period** | 6 months                                                        |
| **Start**       | February 2026 (Month 1)                                         |
| **End**         | July 2026 (Month 6)                                             |
| **Calendar**    | Month 1 = Feb, 2 = Mar, 3 = Apr, 4 = May, 5 = Jun, 6 = Jul 2026 |
| **Production**  | https://floorplan.khbevents.com                                 |

---

## Six-month focus (approved scope)

| Month | Calendar | Focus                                                                                                                                                                                 |
| ----- | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **1** | Feb 2026 | **Production Readiness & Foundation** — Go-live, security (SSL, CSRF, headers, rate limit), backup/restore, monitoring, env config, performance baseline.                             |
| **2** | Mar 2026 | **Core System Focus — Best Core First** — Smart booth/booking/client UX, payment reminder automation, validation & reliability, caching, testing suite, notifications.                |
| **3** | Apr 2026 | **Intelligent CRM & Operational Email** — Segmentation, SendGrid/Mailgun, email automation, delivery analytics, communication history, CRM features.                                  |
| **4** | May 2026 | **Intelligent HR Attendance & PMS Module** — Attendance UX, reporting, GPS/location, leave automation, HR dashboard, **new PMS** (projects, tasks, timeline, resources, integration). |
| **5** | Jun 2026 | **Intelligent Reporting & Marketing Module** — Report builder, analytics/forecasting, export/distribution, **new Marketing module** (campaigns, content calendar, ROI, leads).        |
| **6** | Jul 2026 | **Payment Gateway, Integrations & Final Polish** — ABA/Wing integration, Telegram bot, API expansion, SMS, bug/performance, UX polish, automation, final intelligence.                |

---

## Scope rule

- **In scope:** Only work described in the CEO-approved System Development Detail Plan (and the 6-month focus above).
- **Out of scope:** Any feature or task not in that plan. Do not add it unless the CEO approves it as a change request.
- **Order:** Follow the month-by-month order. Complete or advance the current month before jumping to later months.

---

## Where to see task-level detail

For a full task checklist (month-by-month deliverables and checkboxes), use the **System Development Detail Plan** PDF/DOCX in Approved Documents. Any in-repo checklist or progress log should align with that plan and with this file.

**Last updated:** January 2026  
**Tied to:** CEO-approved System Development Detail Plan (Approved Documents)
