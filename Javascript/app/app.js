// Load jQuery and UIkit
global.jQuery = require('jquery')
global.$ = global.jQuery;
require('uikit')
require('uikit/dist/js/components/pagination')

import React from 'react';
import ReactDOM from 'react-dom';
import {
	Router,
	Route,
	IndexRoute,
	IndexRedirect,
	Link,
} from 'react-router'
import Backbone from 'backbone'
import { browserHistory } from 'react-router'

import {
	MasterLedgerHandler,
	EconomyOverviewHandler,
	EconomyDebugHandler,
} from './Economy/Other'

import {
	EconomyAccountingInstructionsHandler,
	EconomyAccountingInstructionHandler,
	EconomyAccountingInstructionImportHandler,
} from './Economy/Instruction'

import {
	EconomyAccountsHandler,
	EconomyAccountHandler,
	EconomyAccountEditHandler,
	EconomyAccountAddHandler,
} from './Economy/Account'

import {
	EconomyValuationSheetHandler,
} from './Economy/ValuationSheet'

import {
	EconomyResultReportHandler,
} from './Economy/ResultReport'

import {
	EconomyCostCentersHandler,
	EconomyCostCenterHandler,
} from './Economy/CostCenter'

import { InvoiceListHandler, InvoiceHandler, InvoiceAddHandler } from './Economy/Invoice'

import { GroupsHandler, GroupHandler, GroupAddHandler } from './groups'
import { LabAccessHandler } from './labaccess'
import { MembersHandler, MemberHandler, MemberAddHandler } from './member'
import { MailHandler } from './mail'
import {
	Nav,
	SideNav,
	SideNav2,
	Breadcrumb,
} from './nav'
import { SettingsHandler } from './settings'
import { StatisticsHandler } from './statistics'
import { DashboardHandler, ExportHandler, Loading } from './temp'

var nav = new Backbone.Model({
	brand: "Makerspace Internal v2",
	navItems:
	[
		{
			text: "Medlemmar",
			target: "/member",
			icon: "user",
			children:
			[
				{
					text: "Visa medlemmar",
					target: "/member/list",
					children: [
						{
							text: "Medlem",
							target: "/member/:id",
						},
					],
				},
				{
					text: "Skapa medlem",
					target: "/member/add",
				},
				{
					text: "Grupper",
					type: "heading",
					target: "",
				},
				{
					text: "Visa grupper",
					target: "/member/group/list",
				},
				{
					text: "Skapa grupp",
					target: "/member/group/add",
				},
			],
		},
		{
			text: "Prenumerationer",
			target: "/labaccess",
			icon: "refresh",
			children:
			[
				{
					text: "Prenumerationer",
					target: "/labaccess/list",
				},
				{
					text: "Typer",
					target: "/labaccess/types",
				},
			],
		},
		{
			text: "Ekonomi",
			target: "/economy",
			icon: "money",
			children:
			[
				{
					text: "Översikt",
					target: "/economy/overview",
				},
				{
					text: "Huvudbok",
					target: "/economy/masterledger",
				},
				{
					text: "Verifikationer",
					target: "/economy/instruction",
					children:
					[
						{
							text: "",
							target: "/economy/instruction/:id",
						},
					],
				},
				{
					text: "Fakturor",
					target: "/economy/invoice",
					children:
					[
						{
							text: "",
							target: "/economy/invoice/:id",
						},
					],
				},
				{
					text: "Balansrapport",
					target: "/economy/valuationsheet",
				},
				{
					text: "Resultatrapport",
					target: "/economy/resultreport",
				},
				{
					text: "Kostnadsställen",
					target: "/economy/costcenter",
				},
				{
					type: "separator",
					target: "",
				},
				{
					type: "heading",
					text: "Inställningar",
					target: "",
				},
				{
					text: "Konton",
					target: "/economy/accounts",
				},
				{
					text: "Räkneskapsår",
					target: "/economy/accountingperiods",
				},
				{
					text: "Debug",
					target: "/economy/debug",
				},
			],
		},
		{
			text: "Mail",
			target: "/mail",
			icon: "envelope",
			children:
			[
				{
					text: "Hantera listor",
					target: "/mail/lists",
					// TODO: Lista, skapa
				},
				{
					text: "Hantera mallar",
					target: "/mail/templates",
					// TODO: Lista, skapa
				},
				{
					text: "Skicka mail",
					target: "/mail/send",
					// TODO: Till grupp, enskild medlem, filter
				},
				{
					text: "Historik",
					target: "/mail/history",
					// TODO: Historik
				},
			],
		},
		{
			text: "Inställningar",
			target: "settings",
			icon: "cog",
			// TODO: Inställningar
		},
		{
			text: "Export",
			target: "members/export",
			icon: "download",
		},
		{
			text: "Statistik",
			target: "statistics",
			icon: "area-chart",
		},
	]
});

var App = React.createClass({
	render: function()
	{
		var key = this.props.location.pathname;

		return (
			<div>
				<Nav model={nav} />
				<SideNav model={nav} />

				<div className="uk-container uk-container-center uk-margin-top">
					<div className="uk-grid">

						<div className="uk-width-medium-1-4">
							<SideNav2 model={nav} />
						</div>

						<div className="uk-width-medium-3-4">
							<Breadcrumb routes={this.props.routes}/>
							{this.props.children}
						</div>
					</div>
				</div>
			</div>
		);
	}
});
App.title = "Internal"

var NoMatch = React.createClass({
	render: function()
	{
		return (<h1>404</h1>);
	}
});

var NotImplemented = React.createClass({
	render: function()
	{
		return (<h1>Not implemented</h1>);
	}
});

ReactDOM.render((
	<Router history={browserHistory}>
		<Route path="/" component={App}>
			<IndexRoute component={DashboardHandler} />
			<Route path="member">
				<IndexRedirect to="list" />
				<Route path="add"  component={MemberAddHandler} />
				<Route path="list" component={MembersHandler} />
				<Route path=":id"  component={MemberHandler} />
				<Route path="group">
					<IndexRoute component={GroupsHandler} />
					<Route path="list"     component={GroupsHandler} />
					<Route path="add"      component={GroupAddHandler} />
					<Route path=":id"      component={GroupHandler} />
				</Route>
			</Route>
			<Route path="labaccess">
				<IndexRedirect to="list" />
				<Route path="list"  component={LabAccessHandler} />
				<Route path="types" component={NotImplemented} />
			</Route>
			<Route path="economy">
				<IndexRedirect to="overview" />

				<Route path="overview"         component={EconomyOverviewHandler} />
				<Route path="masterledger"     component={MasterLedgerHandler} />

				<Route path="invoice">
					<IndexRedirect to="list" />
					<Route path="list"     component={InvoiceListHandler} />
					<Route path="add"      component={InvoiceAddHandler} />
					<Route path=":id"      component={InvoiceHandler} />
				</Route>

				<Route path="accounts"         component={EconomyAccountsHandler} />
				<Route path="account/add"      component={EconomyAccountAddHandler} />
				<Route path="account/:id"      component={EconomyAccountHandler} />
				<Route path="account/:id/edit" component={EconomyAccountEditHandler} />

				<Route path="instruction"      component={EconomyAccountingInstructionsHandler} />
				<Route path="instruction/:id"  component={EconomyAccountingInstructionHandler} />
				<Route path="instruction/:id/import" component={EconomyAccountingInstructionImportHandler} />

				<Route path="valuationsheet"   component={EconomyValuationSheetHandler} />
				<Route path="resultreport"     component={EconomyResultReportHandler} />

				<Route path="costcenter"       component={EconomyCostCentersHandler} />
				<Route path="costcenter/:id"   component={EconomyCostCenterHandler} />

				<Route path="debug"            component={EconomyDebugHandler} />
			</Route>
			<Route path="members/export" component={ExportHandler} />
			<Route path="statistics"     component={StatisticsHandler} />
			<Route path="mail"           component={MailHandler} />
			<Route path="settings"       component={SettingsHandler} />
			<Route path="*"              component={NoMatch}/>
		</Route>
	</Router>
), document.getElementById("main"));