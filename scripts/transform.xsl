<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xlink="http://www.w3.org/1999/xlink">

	<xsl:output method="xml" indent="yes"/>

	<xsl:template match="*"/>

	<xsl:template match="/PubmedArticleSet">
		<Articles>
			<xsl:apply-templates select="PubmedArticle/MedlineCitation"/>
		</Articles>
	</xsl:template>

	<xsl:template match="MedlineCitation">
		<Article>
			<xsl:apply-templates select="Article/Journal/Title[1]"/>
			<xsl:apply-templates select="Article/Journal/ISSN[1]"/>
			<xsl:apply-templates select="Article/ArticleDate/Year[1]"/>
		</Article>
	</xsl:template>

	<xsl:template match="ISSN | Title | Year">
		<xsl:element name="{local-name()}">
			<xsl:value-of select="."/>
		</xsl:element>
	</xsl:template>
</xsl:stylesheet>