import sporteventComponent from "./components/sporteventComponent";
import Organization from "./pages/Organization";
import sporteventcity from "./pages/sporteventcity";
import sporteventTitle from "./pages/sporteventTitle";

const routes = [
    {
        path: "/",
        component: sporteventComponent
    },
    {
        path: "/sportevents-by-organization",
        component: Organization
    },
    {
        path: "/sportevents-by-title",
        component: sporteventTitle
    },
    {
        path: "/sportevents-by-city",
        component: sporteventcity
    }
];
export default routes;
